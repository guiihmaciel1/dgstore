<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Log;

class OlxScraperService
{
    /**
     * URL base do OLX para iPhones em São José do Rio Preto - SP.
     */
    private const BASE_URL = 'https://www.olx.com.br/eletronicos-e-celulares/celulares/iphone/estado-sp/regiao-de-sao-jose-do-rio-preto/sao-jose-do-rio-preto';

    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ];

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 5;
    private const MAX_RETRIES = 2;

    /** @var \Closure|null Callback para exibir progresso no console */
    private ?\Closure $onProgress = null;

    public function onProgress(\Closure $callback): self
    {
        $this->onProgress = $callback;
        return $this;
    }

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
                Log::info("[OLX Scraper] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  {$model->name}: ERRO - {$e->getMessage()}", 'error');
                Log::error("[OLX Scraper] Erro ao raspar {$model->name}: {$e->getMessage()}");
            }

            $this->randomDelay();
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
            $searchTerm = $model->search_term . ' ' . strtolower($storage);
            $listings = $this->fetchListings($searchTerm);

            foreach ($listings as $listing) {
                $this->saveListing($model, $storage, $listing);
                $totalCount++;
            }

            if (count($model->storages) > 1) {
                $this->randomDelay();
            }
        }

        return $totalCount;
    }

    private function fetchListings(string $searchTerm): array
    {
        $url = self::BASE_URL . '?' . http_build_query(['q' => $searchTerm]);

        $html = $this->fetchWithRetry($url);

        if (!$html) {
            return [];
        }

        return $this->parseListings($html);
    }

    /**
     * Faz o request HTTP via cURL nativo com retry.
     */
    private function fetchWithRetry(string $url): ?string
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_ENCODING => '',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: ' . $this->randomUserAgent(),
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Accept-Encoding: gzip, deflate, br',
                    'Cache-Control: no-cache',
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
                Log::warning("[OLX Scraper] cURL error para {$url} (tentativa {$attempt}): {$error}");
            } elseif ($httpCode === 200 && $body && strlen($body) > 1000) {
                return $body;
            } else {
                Log::warning("[OLX Scraper] HTTP {$httpCode} para {$url} (tentativa {$attempt})");
            }

            if ($attempt < self::MAX_RETRIES) {
                sleep($attempt * 3);
            }
        }

        return null;
    }

    private function parseListings(string $html): array
    {
        $listings = $this->parseFromNextData($html);

        if (empty($listings)) {
            $listings = $this->parseFromHtml($html);
        }

        return $listings;
    }

    private function parseFromNextData(string $html): array
    {
        if (!preg_match('/<script\s+id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $matches)) {
            return [];
        }

        $data = json_decode($matches[1], true);

        if (!$data) {
            return [];
        }

        $ads = $this->extractAdsFromNextData($data);
        $listings = [];

        foreach ($ads as $ad) {
            $price = $this->extractPrice($ad);

            if ($price <= 0) {
                continue;
            }

            $listings[] = [
                'title' => $ad['title'] ?? $ad['subject'] ?? '',
                'price' => $price,
                'url' => $ad['url'] ?? $ad['listId'] ?? null,
                'location' => $this->extractLocation($ad),
            ];
        }

        return $listings;
    }

    private function extractAdsFromNextData(array $data): array
    {
        $paths = [
            ['props', 'pageProps', 'ads'],
            ['props', 'pageProps', 'searchResult', 'ads'],
            ['props', 'pageProps', 'adList'],
        ];

        foreach ($paths as $path) {
            $current = $data;
            foreach ($path as $key) {
                $current = $current[$key] ?? null;
                if ($current === null) {
                    break;
                }
            }

            if (is_array($current) && !empty($current)) {
                return $current;
            }
        }

        return $this->findAdsRecursive($data, 0);
    }

    private function findAdsRecursive(array $data, int $depth): array
    {
        if ($depth > 5) {
            return [];
        }

        foreach ($data as $value) {
            if (!is_array($value)) {
                continue;
            }

            if (isset($value[0]) && is_array($value[0])) {
                $firstItem = $value[0];
                if (isset($firstItem['price']) || isset($firstItem['title']) || isset($firstItem['subject'])) {
                    return $value;
                }
            }

            $result = $this->findAdsRecursive($value, $depth + 1);
            if (!empty($result)) {
                return $result;
            }
        }

        return [];
    }

    private function extractPrice(array $ad): float
    {
        if (isset($ad['price'])) {
            if (is_numeric($ad['price'])) {
                return (float) $ad['price'];
            }

            if (is_string($ad['price'])) {
                return $this->parsePrice($ad['price']);
            }

            if (is_array($ad['price'])) {
                if (isset($ad['price']['value'])) {
                    return (float) $ad['price']['value'];
                }
                if (isset($ad['price']['formattedValue'])) {
                    return $this->parsePrice($ad['price']['formattedValue']);
                }
            }
        }

        if (isset($ad['priceValue']) && is_numeric($ad['priceValue'])) {
            return (float) $ad['priceValue'];
        }

        return 0.0;
    }

    private function parsePrice(string $priceString): float
    {
        $clean = preg_replace('/[^\d.,]/', '', $priceString);

        if (str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } else {
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
                $clean = str_replace('.', '', $clean);
            }
        }

        return (float) $clean;
    }

    private function extractLocation(array $ad): ?string
    {
        if (!isset($ad['location'])) {
            return null;
        }

        if (is_string($ad['location'])) {
            return $ad['location'];
        }

        if (is_array($ad['location'])) {
            $parts = array_filter([
                $ad['location']['neighbourhood'] ?? null,
                $ad['location']['municipality'] ?? null,
                $ad['location']['city'] ?? null,
                $ad['location']['uf'] ?? $ad['location']['state'] ?? null,
            ]);

            return !empty($parts) ? implode(', ', $parts) : null;
        }

        return null;
    }

    private function parseFromHtml(string $html): array
    {
        $listings = [];

        if (preg_match_all('/data-ds-component="DS-AdCard".*?<\/a>/s', $html, $cardMatches)) {
            foreach ($cardMatches[0] as $card) {
                $title = '';
                $price = 0.0;
                $url = null;

                if (preg_match('/aria-label="([^"]+)"/', $card, $titleMatch)) {
                    $title = html_entity_decode($titleMatch[1]);
                }

                if (preg_match('/R\$\s*([\d.,]+)/', $card, $priceMatch)) {
                    $price = $this->parsePrice($priceMatch[0]);
                }

                if (preg_match('/href="([^"]+)"/', $card, $urlMatch)) {
                    $url = $urlMatch[1];
                }

                if ($price > 0 && !empty($title)) {
                    $listings[] = [
                        'title' => $title,
                        'price' => $price,
                        'url' => $url,
                        'location' => null,
                    ];
                }
            }
        }

        return $listings;
    }

    private function saveListing(IphoneModel $model, string $storage, array $listing): void
    {
        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $storage,
            'title' => mb_substr($listing['title'], 0, 255),
            'price' => $listing['price'],
            'url' => $listing['url'] ? mb_substr($listing['url'], 0, 255) : null,
            'source' => ListingSource::Olx,
            'location' => $listing['location'] ? mb_substr($listing['location'], 0, 255) : null,
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
