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
    private const API_BASE = 'https://api.mercadolibre.com';

    private const MIN_DELAY = 1;
    private const MAX_DELAY = 2;

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
        if (! $this->isConfigured()) {
            return false;
        }

        $token = ApiToken::forProvider('mercadolivre');

        if ($token && $token->isValid()) {
            return true;
        }

        Log::info('[ML API] Token expirado ou ausente.');

        return false;
    }

    public function isProxyConfigured(): bool
    {
        return !empty(config('services.scraper_proxy.key'));
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

        if (! $response->successful()) {
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

    // ── Token ────────────────────────────────────

    private function getAccessToken(): ?string
    {
        $token = ApiToken::forProvider('mercadolivre');

        if (! $token || ! $token->isValid()) {
            return null;
        }

        return $token->access_token;
    }

    private function http(): \Illuminate\Http\Client\PendingRequest
    {
        $request = Http::timeout(15);
        $accessToken = $this->getAccessToken();

        if ($accessToken) {
            $request = $request->withToken($accessToken);
        }

        return $request;
    }

    // ── Coleta via Catálogo (funciona em qualquer IP) ──

    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        $this->progress('  Modo: API Mercado Livre (catálogo de produtos)');

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model);
                $totalListings += $count;

                $this->progress("  ✓ {$model->name}: {$count} anúncios");
                Log::info("[ML API] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  ✗ {$model->name}: {$e->getMessage()}", 'error');
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

    /**
     * Para cada storage do modelo:
     * 1. /products/search → encontra o product_id no catálogo
     * 2. /products/{id}/items → obtém todos os anúncios com preço
     */
    private function scrapeModel(IphoneModel $model): int
    {
        $totalCount = 0;

        foreach ($model->storages as $storage) {
            $count = $this->scrapeModelStorage($model, $storage);
            $totalCount += $count;

            if (count($model->storages) > 1) {
                sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
            }
        }

        return $totalCount;
    }

    private function scrapeModelStorage(IphoneModel $model, string $storage): int
    {
        // 1) Encontrar o produto no catálogo
        $productId = $this->findCatalogProductId($model, $storage);

        if (! $productId) {
            $this->progress("    ↳ {$storage}: produto não encontrado no catálogo", 'warn');

            return 0;
        }

        // 2) Buscar itens (anúncios) desse produto
        $items = $this->fetchProductItems($productId);

        if (empty($items)) {
            return 0;
        }

        // 3) Salvar no banco
        $count = 0;
        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0);

            if ($price < 800) {
                continue;
            }

            $this->saveItem($model, $storage, $item);
            $count++;
        }

        $this->progress("    ↳ {$storage}: {$count} anúncios (produto: {$productId})");

        return $count;
    }

    /**
     * Busca no catálogo de produtos e retorna o ID do produto
     * que corresponde ao modelo + storage.
     */
    private function findCatalogProductId(IphoneModel $model, string $storage): ?string
    {
        $query = $model->search_term . ' ' . $storage;

        $response = $this->http()->get(self::API_BASE . '/products/search', [
            'site_id' => 'MLB',
            'q' => $query,
            'status' => 'active',
        ]);

        if (! $response->successful()) {
            Log::warning("[ML API] Erro na busca de produto: {$query}", [
                'status' => $response->status(),
            ]);

            return null;
        }

        $products = $response->json()['results'] ?? [];

        return $this->matchProduct($products, $model, $storage);
    }

    /**
     * Filtra os resultados do catálogo para encontrar o produto correto.
     * Precisa ser Apple iPhone, com o modelo e storage exatos.
     */
    private function matchProduct(array $products, IphoneModel $model, string $storage): ?string
    {
        $storageLower = mb_strtolower(str_replace(' ', '', $storage));

        // Extrair palavras-chave do search_term (ex: "15", "pro", "max")
        $keywords = array_filter(
            explode(' ', mb_strtolower($model->search_term)),
            fn (string $w) => $w !== 'iphone' && strlen($w) > 0
        );

        foreach ($products as $product) {
            $name = mb_strtolower($product['name'] ?? '');

            // Deve ser Apple iPhone
            if (! str_contains($name, 'apple') || ! str_contains($name, 'iphone')) {
                continue;
            }

            // Verificar todas as keywords do modelo
            $allMatch = true;
            foreach ($keywords as $keyword) {
                if (! str_contains($name, $keyword)) {
                    $allMatch = false;
                    break;
                }
            }

            if (! $allMatch) {
                continue;
            }

            // Verificar storage via atributos
            $attrs = $product['attributes'] ?? [];
            foreach ($attrs as $attr) {
                if (($attr['id'] ?? '') === 'INTERNAL_MEMORY') {
                    $attrStorage = mb_strtolower(str_replace(' ', '', $attr['value_name'] ?? ''));
                    if ($attrStorage === $storageLower) {
                        return $product['id'];
                    }
                }
            }

            // Fallback: verificar storage no nome
            $nameClean = str_replace([' ', '-', '(', ')'], '', $name);
            if (str_contains($nameClean, $storageLower)) {
                return $product['id'];
            }
        }

        return null;
    }

    /**
     * Busca todos os itens (anúncios de vendedores) de um produto do catálogo.
     */
    private function fetchProductItems(string $productId): array
    {
        $response = $this->http()->get(self::API_BASE . "/products/{$productId}/items");

        if (! $response->successful()) {
            Log::warning("[ML API] Erro ao buscar items do produto {$productId}", [
                'status' => $response->status(),
            ]);

            return [];
        }

        $data = $response->json();
        $results = $data['results'] ?? [];

        Log::info("[ML API] Produto {$productId}: " . count($results) . " itens de " . ($data['paging']['total'] ?? 0));

        return $results;
    }

    private function saveItem(IphoneModel $model, string $storage, array $item): void
    {
        $itemId = $item['item_id'] ?? null;

        // Evita duplicatas do mesmo item no mesmo dia
        if ($itemId) {
            $exists = MarketListing::where('url', "https://produto.mercadolivre.com.br/{$itemId}")
                ->where('scraped_at', now()->toDateString())
                ->exists();

            if ($exists) {
                return;
            }
        }

        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $storage,
            'title' => mb_substr("iPhone {$model->name} {$storage}", 0, 255),
            'price' => (float) ($item['price'] ?? 0),
            'url' => $itemId ? "https://produto.mercadolivre.com.br/{$itemId}" : null,
            'source' => ListingSource::MercadoLivre,
            'location' => $this->extractLocation($item),
            'scraped_at' => now()->toDateString(),
        ]);
    }

    private function extractLocation(array $item): ?string
    {
        $address = $item['seller_address'] ?? null;

        if (! $address) {
            return null;
        }

        $parts = array_filter([
            $address['city']['name'] ?? null,
            $address['state']['name'] ?? null,
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
