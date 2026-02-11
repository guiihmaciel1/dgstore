<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Log;

class OlxScraperService
{
    private const BASE_URL = 'https://www.olx.com.br/eletronicos-e-celulares/celulares/iphone/estado-sp/regiao-de-sao-jose-do-rio-preto/sao-jose-do-rio-preto';

    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    ];

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 5;
    private const MAX_RETRIES = 2;

    /** @var \Closure|null */
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

        $this->progress($this->isProxyEnabled() ? '  Modo: Proxy (ScraperAPI)' : '  Modo: Direto');

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model);
                $totalListings += $count;
                $this->progress("  {$model->name}: {$count} anúncios");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  {$model->name}: ERRO - {$e->getMessage()}", 'error');
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

        return ['total_listings' => $count, 'models_processed' => 1, 'errors' => []];
    }

    private function isProxyEnabled(): bool
    {
        return !empty(config('services.scraper_proxy.key'));
    }

    private function proxyUrl(string $targetUrl): string
    {
        $apiKey = config('services.scraper_proxy.key');
        if (!$apiKey) {
            return $targetUrl;
        }

        $baseUrl = config('services.scraper_proxy.base_url', 'https://api.scraperapi.com');
        return $baseUrl . '?' . http_build_query(['api_key' => $apiKey, 'url' => $targetUrl, 'country_code' => 'br']);
    }

    private function scrapeModel(IphoneModel $model): int
    {
        $totalCount = 0;

        foreach ($model->storages as $storage) {
            $searchTerm = $model->search_term . ' ' . strtolower($storage);
            $targetUrl = self::BASE_URL . '?' . http_build_query(['q' => $searchTerm]);
            $html = $this->fetchWithRetry($targetUrl);

            if ($html) {
                foreach ($this->parseListings($html) as $listing) {
                    $this->saveListing($model, $storage, $listing);
                    $totalCount++;
                }
            }

            if (count($model->storages) > 1) {
                $this->randomDelay();
            }
        }

        return $totalCount;
    }

    private function fetchWithRetry(string $targetUrl): ?string
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            $url = $this->isProxyEnabled() ? $this->proxyUrl($targetUrl) : $targetUrl;
            $timeout = $this->isProxyEnabled() ? 60 : 10;

            $ch = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => $this->isProxyEnabled() ? 30 : 5,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_ENCODING => '',
                CURLOPT_SSL_VERIFYPEER => true,
            ];

            if (!$this->isProxyEnabled()) {
                $options[CURLOPT_HTTPHEADER] = [
                    'User-Agent: ' . $this->randomUserAgent(),
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Upgrade-Insecure-Requests: 1',
                ];
            }

            curl_setopt_array($ch, $options);
            $body = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if (!$error && $httpCode === 200 && $body && strlen($body) > 1000) {
                return $body;
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
        return !empty($listings) ? $listings : $this->parseFromHtml($html);
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

        $ads = $data['props']['pageProps']['ads']
            ?? $data['props']['pageProps']['searchResult']['ads']
            ?? $data['props']['pageProps']['adList']
            ?? [];

        $listings = [];
        foreach ($ads as $ad) {
            $price = $this->extractPrice($ad);
            if ($price > 0) {
                $listings[] = [
                    'title' => $ad['title'] ?? $ad['subject'] ?? '',
                    'price' => $price,
                    'url' => $ad['url'] ?? null,
                    'location' => $this->extractLocation($ad),
                ];
            }
        }

        return $listings;
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
                return (float) ($ad['price']['value'] ?? 0);
            }
        }

        return 0.0;
    }

    private function parsePrice(string $priceString): float
    {
        $clean = preg_replace('/[^\d.,]/', '', $priceString);
        if (str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
            $clean = str_replace('.', '', $clean);
        }

        return (float) $clean;
    }

    private function extractLocation(array $ad): ?string
    {
        if (!isset($ad['location']) || !is_array($ad['location'])) {
            return is_string($ad['location'] ?? null) ? $ad['location'] : null;
        }

        $parts = array_filter([
            $ad['location']['city'] ?? null,
            $ad['location']['uf'] ?? $ad['location']['state'] ?? null,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    private function parseFromHtml(string $html): array
    {
        $listings = [];

        if (preg_match_all('/data-ds-component="DS-AdCard".*?<\/a>/s', $html, $cardMatches)) {
            foreach ($cardMatches[0] as $card) {
                $title = '';
                $price = 0.0;
                $url = null;

                if (preg_match('/aria-label="([^"]+)"/', $card, $m)) {
                    $title = html_entity_decode($m[1]);
                }
                if (preg_match('/R\$\s*([\d.,]+)/', $card, $m)) {
                    $price = $this->parsePrice($m[0]);
                }
                if (preg_match('/href="([^"]+)"/', $card, $m)) {
                    $url = $m[1];
                }

                if ($price > 0 && $title) {
                    $listings[] = ['title' => $title, 'price' => $price, 'url' => $url, 'location' => null];
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
            'location' => isset($listing['location']) ? mb_substr($listing['location'], 0, 255) : null,
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
