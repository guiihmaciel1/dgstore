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
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    ];

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 4;

    private ?string $cookieJar = null;
    private ?string $sessionUserAgent = null;
    private bool $sessionWarmed = false;

    /** @var \Closure|null */
    private ?\Closure $onProgress = null;

    public function onProgress(\Closure $callback): self
    {
        $this->onProgress = $callback;
        return $this;
    }

    public function __destruct()
    {
        $this->cleanupCookieJar();
    }

    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        $this->progress($this->isProxyEnabled()
            ? '  Modo: Proxy (ScraperAPI)'
            : '  Modo: Direto (cookie jar)'
        );

        if (!$this->isProxyEnabled()) {
            $this->warmUpSession();
        }

        foreach ($models as $model) {
            try {
                $count = $this->scrapeModel($model);
                $totalListings += $count;

                $this->progress("  {$model->name}: {$count} anúncios");
                Log::info("[ML Scraper] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                $this->progress("  {$model->name}: ERRO - {$e->getMessage()}", 'error');
                Log::error("[ML Scraper] Erro: {$e->getMessage()}");
            }

            $this->randomDelay();
        }

        $this->cleanupCookieJar();

        return [
            'total_listings' => $totalListings,
            'models_processed' => $models->count(),
            'errors' => $errors,
        ];
    }

    public function scrapeBySlug(string $slug): array
    {
        $model = IphoneModel::where('slug', $slug)->firstOrFail();

        if (!$this->isProxyEnabled()) {
            $this->warmUpSession();
        }

        $count = $this->scrapeModel($model);
        $this->cleanupCookieJar();

        return [
            'total_listings' => $count,
            'models_processed' => 1,
            'errors' => [],
        ];
    }

    // ── Proxy ────────────────────────────────────

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

        return $baseUrl . '?' . http_build_query([
            'api_key' => $apiKey,
            'url' => $targetUrl,
            'country_code' => 'br',
        ]);
    }

    // ── Session ──────────────────────────────────

    private function warmUpSession(): void
    {
        if ($this->sessionWarmed) {
            return;
        }

        $this->cookieJar = tempnam(sys_get_temp_dir(), 'ml_cookies_');
        $this->sessionUserAgent = $this->randomUserAgent();

        $this->progress('  Aquecendo sessão...');
        $this->directCurlRequest('https://www.mercadolivre.com.br');
        sleep(1);
        $this->directCurlRequest('https://lista.mercadolivre.com.br/iphone');
        sleep(1);

        $this->sessionWarmed = true;
    }

    // ── Core ─────────────────────────────────────

    private function scrapeModel(IphoneModel $model): int
    {
        $totalCount = 0;

        foreach ($model->storages as $storage) {
            $listings = $this->fetchListings($model, $storage);
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

    private function fetchListings(IphoneModel $model, string $storage): array
    {
        $searchSlug = Str::slug($model->search_term . ' ' . $storage . ' usado');
        $targetUrl = self::BASE_URL . '/' . $searchSlug;

        $html = $this->fetchPage($targetUrl);

        if (!$html || $this->isBlockedPage($html)) {
            return [];
        }

        return $this->parseJsonLd($html);
    }

    private function fetchPage(string $targetUrl): ?string
    {
        if ($this->isProxyEnabled()) {
            return $this->proxiedCurlRequest($targetUrl);
        }

        return $this->directCurlRequest($targetUrl);
    }

    private function proxiedCurlRequest(string $targetUrl): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->proxyUrl($targetUrl),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200 || !$body || strlen($body) < 1000) {
            Log::warning("[ML Scraper/Proxy] Falha para {$targetUrl}: " . ($error ?: "HTTP {$httpCode}"));
            return null;
        }

        return $body;
    }

    private function directCurlRequest(string $url): ?string
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'User-Agent: ' . ($this->sessionUserAgent ?? $this->randomUserAgent()),
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Sec-Ch-Ua: "Chromium";v="122", "Not(A:Brand";v="24"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
                'Referer: https://www.mercadolivre.com.br/',
            ],
        ];

        if ($this->cookieJar) {
            $options[CURLOPT_COOKIEJAR] = $this->cookieJar;
            $options[CURLOPT_COOKIEFILE] = $this->cookieJar;
        }

        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200 || !$body || strlen($body) < 1000) {
            return null;
        }

        return $body;
    }

    // ── Parsing ──────────────────────────────────

    private function isBlockedPage(string $html): bool
    {
        if (str_contains($html, 'account-verification') && !str_contains($html, '"@type":"Product"')) {
            return true;
        }

        if (strlen($html) < 100000 && !str_contains($html, '"price"') && !str_contains($html, 'application/ld+json')) {
            return true;
        }

        return false;
    }

    private function parseJsonLd(string $html): array
    {
        $listings = [];

        if (!preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $matches)) {
            return $this->parseFromPricePattern($html);
        }

        foreach ($matches[1] as $jsonStr) {
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

    private function parseFromPricePattern(string $html): array
    {
        $listings = [];

        if (preg_match_all('/"price"\s*:\s*(\d+(?:\.\d+)?)/', $html, $matches)) {
            foreach ($matches[1] as $price) {
                $priceFloat = (float) $price;
                if ($priceFloat >= 800 && $priceFloat <= 30000) {
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

    private function filterRelevantListings(array $listings, IphoneModel $model, string $storage): array
    {
        $storageLower = mb_strtolower(str_replace(' ', '', $storage));

        $modelKeywords = array_filter(
            explode(' ', mb_strtolower($model->search_term)),
            fn (string $w) => $w !== 'iphone' && strlen($w) > 0
        );

        return array_values(array_filter($listings, function (array $listing) use ($modelKeywords, $storageLower) {
            $titleLower = mb_strtolower($listing['title']);
            $titleClean = str_replace([' ', '-', '_'], '', $titleLower);

            foreach ($modelKeywords as $keyword) {
                if (!str_contains($titleLower, $keyword)) {
                    return false;
                }
            }

            if (!str_contains($titleClean, $storageLower)) {
                return false;
            }

            return $listing['price'] >= 800;
        }));
    }

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

    // ── Helpers ──────────────────────────────────

    private function cleanupCookieJar(): void
    {
        if ($this->cookieJar && file_exists($this->cookieJar)) {
            @unlink($this->cookieJar);
            $this->cookieJar = null;
        }
        $this->sessionWarmed = false;
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
