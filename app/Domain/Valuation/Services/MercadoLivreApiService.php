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
    private const AUTH_URL = 'https://auth.mercadolibre.com.br/authorization';
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
        if (!$this->isConfigured()) {
            return false;
        }

        $token = ApiToken::forProvider('mercadolivre');

        if (!$token) {
            return false;
        }

        if ($token->isValid()) {
            return true;
        }

        // Token expirado - sem refresh token, precisa reconectar
        Log::info('[ML API] Token expirado. Fallback para scraping.');

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

    private function getAccessToken(): string
    {
        $token = ApiToken::forProvider('mercadolivre');

        if (!$token) {
            throw new \RuntimeException('ML API não conectada.');
        }

        if (!$token->isValid()) {
            throw new \RuntimeException('Token ML expirado. Reconecte com: php artisan valuation:ml-connect');
        }

        return $token->access_token;
    }

    public function search(string $query, int $limit = 50, int $offset = 0): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->timeout(15)
            ->get(self::SEARCH_URL, [
                'q' => $query,
                'condition' => 'used',
                'category' => 'MLB1055',
                'limit' => $limit,
                'offset' => $offset,
                'sort' => 'relevance',
            ]);

        if (!$response->successful()) {
            Log::warning("[ML API] HTTP {$response->status()} para busca: {$query}");
            return [];
        }

        $data = $response->json();

        return [
            'total' => $data['paging']['total'] ?? 0,
            'items' => $data['results'] ?? [],
        ];
    }

    // ── Scraping via API ─────────────────────────

    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        $this->progress('  Modo: API Mercado Livre (OAuth2)');

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
