<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Log;

class FacebookMarketplaceService
{
    private const GRAPHQL_URL = 'https://www.facebook.com/api/graphql/';

    private const LOCATION_DOC_ID = '5585904654783609';
    private const SEARCH_DOC_ID = '7111939778879383';

    private const DEFAULT_RADIUS_KM = 40;
    private const RESULTS_PER_PAGE = 24;
    private const MIN_PRICE = 800;
    private const MAX_PRICE = 50000;

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 4;

    private ?\Closure $onProgress = null;

    public function onProgress(\Closure $callback): self
    {
        $this->onProgress = $callback;

        return $this;
    }

    // ── Localização ─────────────────────────────

    /**
     * Busca coordenadas (lat/lon) para uma cidade usando a API GraphQL do Facebook.
     *
     * @return array{name: string, latitude: float, longitude: float}[]
     */
    public function getLocations(string $query): array
    {
        $variables = json_encode([
            'params' => [
                'caller' => 'MARKETPLACE',
                'page_category' => ['CITY', 'SUBCITY', 'NEIGHBORHOOD', 'POSTAL_CODE'],
                'query' => $query,
            ],
        ]);

        $response = $this->graphqlRequest([
            'variables' => $variables,
            'doc_id' => self::LOCATION_DOC_ID,
        ]);

        if (! $response) {
            return [];
        }

        $locations = [];
        $edges = $response['data']['city_street_search']['street_results']['edges'] ?? [];

        foreach ($edges as $edge) {
            $node = $edge['node'] ?? [];
            $name = $node['subtitle'] ?? '';

            // Refinar nome se muito genérico
            if ($name === 'City' || str_contains($name, '·')) {
                $name = explode(' ·', $name)[0];
            }

            if (empty($name) || $name === 'City') {
                $name = $node['single_line_address'] ?? $query;
            }

            $locations[] = [
                'name' => $name,
                'latitude' => (float) ($node['location']['latitude'] ?? 0),
                'longitude' => (float) ($node['location']['longitude'] ?? 0),
            ];
        }

        return $locations;
    }

    // ── Busca de Anúncios ───────────────────────

    /**
     * Busca anúncios no Facebook Marketplace via GraphQL.
     *
     * @return array{listings: array, hasNextPage: bool, cursor: ?string}
     */
    public function getListings(
        float $latitude,
        float $longitude,
        string $query,
        int $numPages = 1,
        ?string $cursor = null,
    ): array {
        $allListings = [];

        for ($page = 0; $page < $numPages; $page++) {
            $variables = $this->buildSearchVariables($latitude, $longitude, $query, $cursor);

            $response = $this->graphqlRequest([
                'variables' => $variables,
                'doc_id' => self::SEARCH_DOC_ID,
            ]);

            if (! $response) {
                break;
            }

            $feedUnits = $response['data']['marketplace_search']['feed_units'] ?? [];
            $edges = $feedUnits['edges'] ?? [];
            $pageInfo = $feedUnits['page_info'] ?? [];

            foreach ($edges as $edge) {
                $listing = $this->parseListingEdge($edge);
                if ($listing) {
                    $allListings[] = $listing;
                }
            }

            $hasNextPage = $pageInfo['has_next_page'] ?? false;
            $cursor = $pageInfo['end_cursor'] ?? null;

            if (! $hasNextPage) {
                break;
            }

            // Delay entre páginas
            if ($page < $numPages - 1) {
                sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
            }
        }

        return [
            'listings' => $allListings,
            'hasNextPage' => $hasNextPage ?? false,
            'cursor' => $cursor,
        ];
    }

    // ── Coleta Principal ────────────────────────

    /**
     * Coleta anúncios de iPhones usados no Facebook Marketplace.
     */
    public function scrapeAll(string $locationQuery, int $pages = 2): array
    {
        $models = IphoneModel::active()->get();
        $coords = $this->resolveLocation($locationQuery);

        if (! $coords) {
            $this->progress("  ✗ Localização não encontrada: {$locationQuery}", 'error');

            return ['total_listings' => 0, 'models_processed' => 0, 'errors' => ["Localização não encontrada: {$locationQuery}"]];
        }

        $this->progress("  Localização: {$coords['name']} ({$coords['latitude']}, {$coords['longitude']})");

        $totalListings = 0;
        $errors = [];

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model, $coords['latitude'], $coords['longitude'], $pages);
                $totalListings += $count;

                $this->progress("  ✓ {$model->name}: {$count} anúncios");
                Log::info("[FB Marketplace] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  ✗ {$model->name}: {$e->getMessage()}", 'error');
                Log::error("[FB Marketplace] Erro ao buscar {$model->name}: {$e->getMessage()}");
            }

            sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
        }

        return [
            'total_listings' => $totalListings,
            'models_processed' => $models->count(),
            'errors' => $errors,
        ];
    }

    public function scrapeBySlug(string $slug, string $locationQuery, int $pages = 2): array
    {
        $model = IphoneModel::where('slug', $slug)->firstOrFail();
        $coords = $this->resolveLocation($locationQuery);

        if (! $coords) {
            return ['total_listings' => 0, 'models_processed' => 0, 'errors' => ["Localização não encontrada: {$locationQuery}"]];
        }

        $this->progress("  Localização: {$coords['name']} ({$coords['latitude']}, {$coords['longitude']})");

        $count = $this->scrapeModel($model, $coords['latitude'], $coords['longitude'], $pages);

        return [
            'total_listings' => $count,
            'models_processed' => 1,
            'errors' => [],
        ];
    }

    // ── Métodos Privados ────────────────────────

    private function resolveLocation(string $locationQuery): ?array
    {
        // Coordenadas padrão se for São José do Rio Preto
        $defaults = [
            'são josé do rio preto' => ['name' => 'São José do Rio Preto, SP', 'latitude' => -20.8167, 'longitude' => -49.3833],
            'sao jose do rio preto' => ['name' => 'São José do Rio Preto, SP', 'latitude' => -20.8167, 'longitude' => -49.3833],
        ];

        $queryLower = mb_strtolower($locationQuery);

        if (isset($defaults[$queryLower])) {
            $this->progress("  Usando coordenadas padrão para: {$defaults[$queryLower]['name']}");

            return $defaults[$queryLower];
        }

        // Consultar GraphQL do Facebook para obter coordenadas
        $locations = $this->getLocations($locationQuery);

        if (empty($locations)) {
            return null;
        }

        return $locations[0];
    }

    private function scrapeModel(IphoneModel $model, float $lat, float $lon, int $pages): int
    {
        $searchTerm = $model->search_term;

        $result = $this->getListings($lat, $lon, $searchTerm, $pages);
        $listings = $result['listings'];

        if (empty($listings)) {
            $this->progress("    ↳ Nenhum anúncio encontrado para: {$searchTerm}", 'warn');

            return 0;
        }

        $count = 0;
        foreach ($listings as $listing) {
            $price = $listing['price'];

            // Filtro de preço razoável
            if ($price < self::MIN_PRICE || $price > self::MAX_PRICE) {
                continue;
            }

            // Verificar se o título realmente corresponde ao modelo
            if (! $this->titleMatchesModel($listing['name'], $model)) {
                continue;
            }

            $this->saveListing($model, $listing);
            $count++;
        }

        return $count;
    }

    /**
     * Verifica se o título do anúncio corresponde ao modelo de iPhone.
     * Evita que um anúncio de iPhone 12 seja atribuído ao iPhone 15 Pro Max.
     */
    private function titleMatchesModel(string $title, IphoneModel $model): bool
    {
        $titleLower = mb_strtolower($title);

        // Deve conter "iphone"
        if (! str_contains($titleLower, 'iphone')) {
            return false;
        }

        // Extrair keywords do search_term (ex: "iphone 15 pro max" → ["15", "pro", "max"])
        $keywords = array_filter(
            explode(' ', mb_strtolower($model->search_term)),
            fn (string $w) => $w !== 'iphone' && strlen($w) > 0
        );

        // Todas as keywords devem estar presentes no título
        foreach ($keywords as $keyword) {
            if (! str_contains($titleLower, $keyword)) {
                return false;
            }
        }

        // Evitar falso-positivo: "iPhone 15" não deve matchear "iPhone 15 Pro"
        // Se o modelo NÃO é Pro/Pro Max, rejeitar se o título contém "pro"
        $modelLower = mb_strtolower($model->search_term);
        if (! str_contains($modelLower, 'pro') && str_contains($titleLower, 'pro')) {
            return false;
        }

        // Se o modelo NÃO é Max, rejeitar se o título contém "max"
        if (! str_contains($modelLower, 'max') && str_contains($titleLower, 'max')) {
            return false;
        }

        // Se o modelo NÃO é Mini/Plus, rejeitar se o título contém "mini" ou "plus"
        if (! str_contains($modelLower, 'mini') && str_contains($titleLower, 'mini')) {
            return false;
        }
        if (! str_contains($modelLower, 'plus') && str_contains($titleLower, 'plus')) {
            return false;
        }

        return true;
    }

    private function saveListing(IphoneModel $model, array $listing): void
    {
        $listingId = $listing['id'] ?? null;
        $url = $listingId ? "https://www.facebook.com/marketplace/item/{$listingId}" : null;

        // Evitar duplicatas do mesmo item no mesmo dia
        if ($url) {
            $exists = MarketListing::where('url', $url)
                ->where('scraped_at', now()->toDateString())
                ->exists();

            if ($exists) {
                return;
            }
        }

        // Tentar detectar o storage a partir do título
        $storage = $this->detectStorage($listing['name']);

        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $storage,
            'title' => mb_substr($listing['name'], 0, 255),
            'price' => $listing['price'],
            'url' => $url,
            'source' => ListingSource::FacebookMarketplace,
            'location' => $listing['sellerLocation'] ?? null,
            'scraped_at' => now()->toDateString(),
        ]);
    }

    /**
     * Tenta detectar o storage (64GB, 128GB, etc.) a partir do título do anúncio.
     */
    private function detectStorage(string $title): ?string
    {
        $storages = ['1TB', '512GB', '256GB', '128GB', '64GB', '32GB'];

        $titleUpper = mb_strtoupper(str_replace(' ', '', $title));

        foreach ($storages as $storage) {
            if (str_contains($titleUpper, $storage)) {
                return $storage;
            }
        }

        return null;
    }

    // ── GraphQL Request ─────────────────────────

    private function graphqlRequest(array $payload): ?array
    {
        $headers = [
            'sec-fetch-site: same-origin',
            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'accept: */*',
            'accept-language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
            'content-type: application/x-www-form-urlencoded',
            'origin: https://www.facebook.com',
            'referer: https://www.facebook.com/marketplace/',
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => self::GRAPHQL_URL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_ENCODING => 'gzip, deflate',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            Log::error("[FB Marketplace] cURL error: {$error}");

            return null;
        }

        if ($httpCode !== 200) {
            Log::warning("[FB Marketplace] HTTP {$httpCode}", [
                'body' => mb_substr((string) $response, 0, 500),
            ]);

            return null;
        }

        $data = json_decode((string) $response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[FB Marketplace] JSON decode error: ' . json_last_error_msg(), [
                'body' => mb_substr((string) $response, 0, 500),
            ]);

            return null;
        }

        // Verificar erros do Facebook
        if (isset($data['errors'])) {
            $errorMsg = $data['errors'][0]['message'] ?? 'Unknown error';
            Log::warning("[FB Marketplace] GraphQL error: {$errorMsg}");

            return null;
        }

        return $data;
    }

    private function buildSearchVariables(float $lat, float $lon, string $query, ?string $cursor = null): string
    {
        $variables = [
            'count' => self::RESULTS_PER_PAGE,
            'params' => [
                'bqf' => [
                    'callsite' => 'COMMERCE_MKTPLACE_WWW',
                    'query' => $query,
                ],
                'browse_request_params' => [
                    'commerce_enable_local_pickup' => true,
                    'commerce_enable_shipping' => true,
                    'commerce_search_and_rp_available' => true,
                    'commerce_search_and_rp_condition' => null,
                    'commerce_search_and_rp_ctime_days' => null,
                    'filter_location_latitude' => $lat,
                    'filter_location_longitude' => $lon,
                    'filter_price_lower_bound' => 0,
                    'filter_price_upper_bound' => 214748364700,
                    'filter_radius_km' => self::DEFAULT_RADIUS_KM,
                ],
                'custom_request_params' => [
                    'surface' => 'SEARCH',
                ],
            ],
        ];

        if ($cursor) {
            $variables['cursor'] = $cursor;
        }

        return json_encode($variables);
    }

    /**
     * Extrai os dados relevantes de um edge do GraphQL.
     */
    private function parseListingEdge(array $edge): ?array
    {
        $node = $edge['node'] ?? [];

        // Verificar se é um listing (não um anúncio patrocinado ou outro tipo)
        if (($node['__typename'] ?? '') !== 'MarketplaceFeedListingStoryObject') {
            return null;
        }

        $listing = $node['listing'] ?? [];

        $price = 0;
        $priceStr = $listing['listing_price']['formatted_amount'] ?? '';

        // Converter preço formatado (ex: "R$4.500" ou "R$ 4.500,00") para float
        if ($priceStr) {
            $price = $this->parsePrice($priceStr);
        }

        $sellerLocation = '';
        $locationData = $listing['location']['reverse_geocode']['city_page'] ?? null;

        if ($locationData) {
            $sellerLocation = $locationData['display_name'] ?? '';
        }

        return [
            'id' => $listing['id'] ?? null,
            'name' => $listing['marketplace_listing_title'] ?? '',
            'price' => $price,
            'previousPrice' => $listing['strikethrough_price']['formatted_amount'] ?? null,
            'isPending' => $listing['is_pending'] ?? false,
            'photoUrl' => $listing['primary_listing_photo']['image']['uri'] ?? null,
            'sellerName' => $listing['marketplace_listing_seller']['name'] ?? '',
            'sellerLocation' => $sellerLocation,
            'sellerType' => $listing['marketplace_listing_seller']['__typename'] ?? '',
        ];
    }

    /**
     * Converte preço formatado do FB (ex: "R$1.700", "R$4.500,00") para float.
     * O FB Brasil usa formato: R$1.700 (ponto = milhar, sem decimais).
     */
    private function parsePrice(string $formatted): float
    {
        // Remover símbolo de moeda e espaços
        $clean = preg_replace('/[^\d.,]/', '', $formatted);

        if (! $clean) {
            return 0;
        }

        // Formato BR com decimais: "4.500,00" → vírgula seguida de 2 dígitos no final
        if (preg_match('/,\d{2}$/', $clean)) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);

            return (float) $clean;
        }

        // Formato BR sem decimais: "1.700" → ponto seguido de exatamente 3 dígitos
        // Detecta se todo ponto é seguido por 3 dígitos (separador de milhar)
        if (preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
            $clean = str_replace('.', '', $clean);

            return (float) $clean;
        }

        // Formato US-like: "4,500" ou sem separadores: "4500"
        $clean = str_replace(',', '', $clean);

        return (float) $clean;
    }

    private function progress(string $message, string $type = 'info'): void
    {
        if ($this->onProgress) {
            ($this->onProgress)($message, $type);
        }
    }
}
