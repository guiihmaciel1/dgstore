<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\ApiToken;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoLivreApiService
{
    private const AUTH_URL = 'https://auth.mercadolivre.com.br/authorization';
    private const TOKEN_URL = 'https://api.mercadolibre.com/oauth/token';
    private const SEARCH_URL = 'https://api.mercadolibre.com/sites/MLB/search';

    private const MIN_DELAY = 1;
    private const MAX_DELAY = 2;

    /** @var \Closure|null */
    private ?\Closure $onProgress = null;

    public function onProgress(\Closure $callback): self
    {
        $this->onProgress = $callback;
        return $this;
    }

    // ── Verificação ──────────────────────────────

    public function isConfigured(): bool
    {
        return !empty(config('services.mercadolivre.client_id'))
            && !empty(config('services.mercadolivre.client_secret'));
    }

    public function isConnected(): bool
    {
        // Conexão via token OAuth
        if ($this->isConfigured()) {
            $token = ApiToken::forProvider('mercadolivre');

            if ($token && $token->isValid()) {
                return true;
            }
        }

        // Conexão via proxy (busca pública, sem token)
        if ($this->isProxyConfigured()) {
            return true;
        }

        if ($this->isConfigured()) {
            Log::info('[ML API] Token expirado e proxy não configurado. Fallback para scraping.');
        }

        return false;
    }

    // ── OAuth2 ───────────────────────────────────

    public function getAuthorizationUrl(): string
    {
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.mercadolivre.client_id'),
            'redirect_uri' => config('services.mercadolivre.redirect_uri'),
        ]);

        return self::AUTH_URL . '?' . $params;
    }

    public function exchangeCode(string $code): ApiToken
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.mercadolivre.client_id'),
            'client_secret' => config('services.mercadolivre.client_secret'),
            'code' => $code,
            'redirect_uri' => config('services.mercadolivre.redirect_uri'),
        ]);

        if (!$response->successful()) {
            $error = $response->json('error', 'unknown');
            $desc = $response->json('error_description', '');
            throw new \RuntimeException("Erro ao trocar código: {$error} - {$desc}");
        }

        return $this->saveToken($response->json());
    }

    private function saveToken(array $data): ApiToken
    {
        return ApiToken::updateOrCreate(
            ['provider' => 'mercadolivre'],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'] ?? '',
                'expires_at' => now()->addSeconds($data['expires_in'] ?? 10800),
                'external_user_id' => $data['user_id'] ?? null,
                'scopes' => isset($data['scope']) ? explode(' ', $data['scope']) : null,
            ]
        );
    }

    // ── Search API ───────────────────────────────

    private function getAccessToken(): ?string
    {
        $token = ApiToken::forProvider('mercadolivre');

        if (!$token || !$token->isValid()) {
            return null;
        }

        return $token->access_token;
    }

    public function search(string $query, int $limit = 50, int $offset = 0): array
    {
        $params = [
            'q' => $query,
            'condition' => 'used',
            'category' => 'MLB1055',
            'limit' => $limit,
            'offset' => $offset,
            'sort' => 'relevance',
        ];

        // 1) Tenta chamada direta com token (se disponível)
        $accessToken = $this->getAccessToken();

        if ($accessToken) {
            $response = Http::withToken($accessToken)
                ->timeout(15)
                ->get(self::SEARCH_URL, $params);

            if ($response->successful()) {
                return $this->parseSearchResponse($response->json(), $query, 'direto');
            }

            // Se não for 403, é outro erro — loggar e retornar vazio
            if ($response->status() !== 403) {
                Log::warning("[ML API] HTTP {$response->status()} para busca: {$query}", [
                    'body' => mb_substr($response->body(), 0, 500),
                ]);

                return ['total' => 0, 'items' => []];
            }
        }

        // 2) Fallback: ScraperAPI como proxy (busca pública via IP residencial)
        if ($this->isProxyConfigured()) {
            if ($accessToken) {
                $this->progress('    ↳ API direta bloqueada (403), usando proxy...', 'warn');
            }

            return $this->searchViaProxy($params, $query);
        }

        Log::warning("[ML API] Busca bloqueada e proxy não configurado: {$query}");

        return ['total' => 0, 'items' => []];
    }

    /**
     * Busca roteada pelo ScraperAPI (IP residencial).
     * O endpoint /sites/MLB/search é público — não precisa de Bearer token via proxy.
     */
    private function searchViaProxy(array $params, string $query): array
    {
        $targetUrl = self::SEARCH_URL . '?' . http_build_query($params);

        $proxyUrl = config('services.scraper_proxy.base_url', 'https://api.scraperapi.com')
            . '?' . http_build_query([
                'api_key' => config('services.scraper_proxy.key'),
                'url' => $targetUrl,
            ]);

        $response = Http::timeout(30)->get($proxyUrl);

        if (!$response->successful()) {
            Log::warning("[ML API][Proxy] HTTP {$response->status()} para busca: {$query}", [
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            return ['total' => 0, 'items' => []];
        }

        $data = $response->json();

        // ScraperAPI pode retornar HTML se a resposta não for JSON
        if (!is_array($data) || !isset($data['results'])) {
            Log::warning('[ML API][Proxy] Resposta não é JSON válido da API ML.', [
                'body' => mb_substr($response->body(), 0, 300),
            ]);

            return ['total' => 0, 'items' => []];
        }

        return $this->parseSearchResponse($data, $query, 'proxy');
    }

    public function isProxyConfigured(): bool
    {
        return !empty(config('services.scraper_proxy.key'));
    }

    private function parseSearchResponse(array $data, string $query, string $via): array
    {
        $total = $data['paging']['total'] ?? 0;
        $results = $data['results'] ?? [];

        Log::info("[ML API][{$via}] Busca: {$query}", [
            'total' => $total,
            'results_count' => count($results),
        ]);

        return [
            'total' => $total,
            'items' => $results,
        ];
    }

    // ── Scraping via API ─────────────────────────

    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        $token = $this->getAccessToken();
        $proxy = $this->isProxyConfigured();

        if ($token && !$proxy) {
            $this->progress('  Modo: API Mercado Livre (OAuth2)');
        } elseif ($token && $proxy) {
            $this->progress('  Modo: API Mercado Livre (OAuth2 + proxy fallback)');
        } else {
            $this->progress('  Modo: API Mercado Livre (proxy público)');
        }

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model);
                $totalListings += $count;

                $this->progress("  {$model->name}: {$count} anúncios");
                Log::info("[ML API] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  {$model->name}: ERRO - {$e->getMessage()}", 'error');
                Log::error("[ML API] Erro ao buscar {$model->name}: {$e->getMessage()}");
            }

            sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
        }

        return [
            'total_listings' => $totalListings,
            'models_processed' => $models->count(),
            'errors' => $errors,
        ];
    }

    public function scrapeBySlug(string $slug): array
    {
        $model = IphoneModel::where('slug', $slug)->firstOrFail();
        $count = $this->scrapeModel($model);

        return [
            'total_listings' => $count,
            'models_processed' => 1,
            'errors' => [],
        ];
    }

    private function scrapeModel(IphoneModel $model): int
    {
        $totalCount = 0;

        foreach ($model->storages as $storage) {
            $query = $model->search_term . ' ' . $storage;
            $result = $this->search($query);

            $filtered = $this->filterApiResults($result['items'], $model, $storage);

            foreach ($filtered as $item) {
                $this->saveApiListing($model, $storage, $item);
                $totalCount++;
            }

            if (count($model->storages) > 1) {
                sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
            }
        }

        return $totalCount;
    }

    private function filterApiResults(array $items, IphoneModel $model, string $storage): array
    {
        $storageLower = mb_strtolower(str_replace(' ', '', $storage));

        $modelKeywords = array_filter(
            explode(' ', mb_strtolower($model->search_term)),
            fn (string $w) => $w !== 'iphone' && strlen($w) > 0
        );

        return array_values(array_filter($items, function (array $item) use ($modelKeywords, $storageLower) {
            $titleLower = mb_strtolower($item['title'] ?? '');
            $titleClean = str_replace([' ', '-', '_'], '', $titleLower);

            foreach ($modelKeywords as $keyword) {
                if (!str_contains($titleLower, $keyword)) {
                    return false;
                }
            }

            if (!str_contains($titleClean, $storageLower)) {
                return false;
            }

            if (($item['price'] ?? 0) < 800) {
                return false;
            }

            return true;
        }));
    }

    private function saveApiListing(IphoneModel $model, string $storage, array $item): void
    {
        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $storage,
            'title' => mb_substr($item['title'] ?? '', 0, 255),
            'price' => (float) ($item['price'] ?? 0),
            'url' => $item['permalink'] ?? null,
            'source' => ListingSource::MercadoLivre,
            'location' => $this->extractApiLocation($item),
            'scraped_at' => now()->toDateString(),
        ]);
    }

    private function extractApiLocation(array $item): ?string
    {
        $location = $item['address'] ?? $item['seller_address'] ?? null;

        if (!$location) {
            return null;
        }

        $parts = array_filter([
            $location['city']['name'] ?? null,
            $location['state']['name'] ?? null,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    private function progress(string $message, string $type = 'info'): void
    {
        if ($this->onProgress) {
            ($this->onProgress)($message, $type);
        }
    }
}
