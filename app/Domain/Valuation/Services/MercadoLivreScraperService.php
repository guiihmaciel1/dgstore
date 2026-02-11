<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoLivreScraperService
{
    private const BASE_URL = 'https://lista.mercadolivre.com.br';

    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ];

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 4;

    /** @var \Closure|null Callback para exibir progresso no console */
    private ?\Closure $onProgress = null;

    public function onProgress(\Closure $callback): self
    {
        $this->onProgress = $callback;
        return $this;
    }

    /**
     * Executa o scraping para todos os modelos ativos.
     */
    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model);
                $totalListings += $count;

                $this->progress("  {$model->name}: {$count} anúncios");
                Log::info("[ML Scraper] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  {$model->name}: ERRO - {$e->getMessage()}", 'error');
                Log::error("[ML Scraper] Erro ao raspar {$model->name}: {$e->getMessage()}");
            }

            $this->randomDelay();
        }

        return [
            'total_listings' => $totalListings,
            'models_processed' => $models->count(),
            'errors' => $errors,
        ];
    }

    /**
     * Executa o scraping para um modelo específico (por slug).
     */
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
     * Raspa anúncios de um modelo específico.
     */
    private function scrapeModel(IphoneModel $model): int
    {
        $totalCount = 0;

        foreach ($model->storages as $storage) {
            $listings = $this->fetchListings($model, $storage);

            // Filtra listagens que realmente correspondem ao modelo+storage
            $filtered = $this->filterRelevantListings($listings, $model, $storage);

            foreach ($filtered as $listing) {
                $this->saveListing($model, $storage, $listing);
                $totalCount++;
            }

            if (count($model->storages) > 1) {
                $this->randomDelay();
            }
        }

        return $totalCount;
    }

    /**
     * Busca anúncios no Mercado Livre para um modelo+storage.
     */
    private function fetchListings(IphoneModel $model, string $storage): array
    {
        // Monta a URL: lista.mercadolivre.com.br/iphone-16-pro-256gb-usado
        $searchSlug = Str::slug($model->search_term . ' ' . $storage . ' usado');
        $url = self::BASE_URL . '/' . $searchSlug;

        $html = $this->fetchPage($url);

        if (!$html) {
            return [];
        }

        return $this->parseJsonLd($html);
    }

    /**
     * Faz o request HTTP via cURL nativo para máxima compatibilidade.
     */
    private function fetchPage(string $url): ?string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_ENCODING => '',  // Aceita gzip, deflate automaticamente
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'User-Agent: ' . $this->randomUserAgent(),
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
            ],
        ]);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::warning("[ML Scraper] cURL error para {$url}: {$error}");
            return null;
        }

        if ($httpCode !== 200) {
            Log::warning("[ML Scraper] HTTP {$httpCode} para {$url}");
            return null;
        }

        if (!$body || strlen($body) < 1000) {
            Log::warning("[ML Scraper] Resposta vazia/pequena para {$url} (" . strlen($body ?: '') . " bytes)");
            return null;
        }

        return $body;
    }

    /**
     * Extrai produtos do JSON-LD (schema.org) embutido no HTML.
     *
     * O Mercado Livre embute um <script type="application/ld+json"> com
     * @graph contendo objetos Product com name, offers.price, offers.url.
     */
    private function parseJsonLd(string $html): array
    {
        $listings = [];

        // Busca todos os blocos JSON-LD
        if (!preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $matches)) {
            // Fallback: busca JSON com @context schema.org direto em scripts normais
            if (!preg_match('/<script[^>]*>(\{["\']@context["\']:["\']https?:..schema\.org.*?)<\/script>/s', $html, $fallback)) {
                return $this->parseFromPricePattern($html);
            }
            $matches[1] = [$fallback[1]];
        }

        foreach ($matches[1] as $jsonStr) {
            // Decodifica unicode escapes do ML (\u002F → /)
            $jsonStr = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($m) {
                return mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE');
            }, $jsonStr);

            $data = json_decode($jsonStr, true);

            if (!$data || !isset($data['@graph'])) {
                continue;
            }

            foreach ($data['@graph'] as $item) {
                if (($item['@type'] ?? '') !== 'Product') {
                    continue;
                }

                $offers = $item['offers'] ?? [];
                $price = (float) ($offers['price'] ?? 0);

                if ($price <= 0) {
                    continue;
                }

                $listings[] = [
                    'title' => $item['name'] ?? '',
                    'price' => $price,
                    'url' => $offers['url'] ?? null,
                    'location' => null,
                ];
            }
        }

        return $listings;
    }

    /**
     * Fallback: extrai preços via regex no HTML.
     */
    private function parseFromPricePattern(string $html): array
    {
        $listings = [];

        if (preg_match_all('/\{"price":(\d+(?:\.\d+)?)[^}]*\}/', $html, $matches)) {
            foreach ($matches[1] as $price) {
                $priceFloat = (float) $price;
                if ($priceFloat > 500) { // Ignora preços muito baixos (não são iPhones)
                    $listings[] = [
                        'title' => 'iPhone (Mercado Livre)',
                        'price' => $priceFloat,
                        'url' => null,
                        'location' => null,
                    ];
                }
            }
        }

        return $listings;
    }

    /**
     * Filtra listagens que realmente correspondem ao modelo+storage buscado.
     *
     * O ML pode retornar resultados de modelos diferentes. Ex: buscar
     * "iphone 16 pro 256gb" pode trazer "iphone 14 pro 256gb".
     */
    private function filterRelevantListings(array $listings, IphoneModel $model, string $storage): array
    {
        $searchTermLower = mb_strtolower($model->search_term);
        $storageLower = mb_strtolower(str_replace(' ', '', $storage));

        // Extrai os termos-chave do modelo (ex: "iphone 16 pro" → ["16", "pro"])
        $modelKeywords = array_filter(
            explode(' ', $searchTermLower),
            fn (string $w) => $w !== 'iphone' && strlen($w) > 0
        );

        return array_values(array_filter($listings, function (array $listing) use ($modelKeywords, $storageLower) {
            $titleLower = mb_strtolower($listing['title']);
            $titleClean = str_replace([' ', '-', '_'], '', $titleLower);

            // Verifica se todos os termos-chave do modelo estão no título
            foreach ($modelKeywords as $keyword) {
                if (!str_contains($titleLower, $keyword)) {
                    return false;
                }
            }

            // Verifica se o storage está no título
            if (!str_contains($titleClean, $storageLower)) {
                return false;
            }

            // Preço mínimo razoável para um iPhone
            if ($listing['price'] < 800) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Salva um anúncio no banco.
     */
    private function saveListing(IphoneModel $model, string $storage, array $listing): void
    {
        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $storage,
            'title' => mb_substr($listing['title'], 0, 255),
            'price' => $listing['price'],
            'url' => $listing['url'] ? mb_substr($listing['url'], 0, 255) : null,
            'source' => ListingSource::MercadoLivre,
            'location' => $listing['location'],
            'scraped_at' => now()->toDateString(),
        ]);
    }

    private function progress(string $message, string $type = 'info'): void
    {
        if ($this->onProgress) {
            ($this->onProgress)($message, $type);
        }
    }

    private function randomDelay(): void
    {
        sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
    }

    private function randomUserAgent(): string
    {
        return self::USER_AGENTS[array_rand(self::USER_AGENTS)];
    }
}
