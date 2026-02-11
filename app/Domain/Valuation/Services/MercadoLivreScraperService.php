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
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0',
    ];

    private const MIN_DELAY = 2;
    private const MAX_DELAY = 4;

    /** Arquivo temporário para persistir cookies entre requests */
    private ?string $cookieJar = null;

    /** User-Agent fixo por sessão (para consistência) */
    private ?string $sessionUserAgent = null;

    /** Se a sessão já foi aquecida */
    private bool $sessionWarmed = false;

    /** @var \Closure|null Callback para exibir progresso no console */
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

    /**
     * Executa o scraping para todos os modelos ativos.
     */
    public function scrapeAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalListings = 0;
        $errors = [];

        // Inicia sessão uma única vez para todos os models
        $this->warmUpSession();

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

        $this->cleanupCookieJar();

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

        $this->warmUpSession();
        $count = $this->scrapeModel($model);
        $this->cleanupCookieJar();

        return [
            'total_listings' => $count,
            'models_processed' => 1,
            'errors' => [],
        ];
    }

    /**
     * Aquece a sessão visitando o ML para obter cookies de consentimento.
     *
     * O Mercado Livre redireciona para /gz/account-verification em
     * servidores sem cookies. Essa etapa resolve isso.
     */
    private function warmUpSession(): void
    {
        if ($this->sessionWarmed) {
            return;
        }

        $this->cookieJar = tempnam(sys_get_temp_dir(), 'ml_cookies_');
        $this->sessionUserAgent = $this->randomUserAgent();

        $this->progress('  Iniciando sessão no Mercado Livre...');

        // Passo 1: Visitar homepage do ML para obter cookies iniciais
        $this->curlRequest('https://www.mercadolivre.com.br');
        sleep(1);

        // Passo 2: Visitar lista.mercadolivre.com.br para aceitar redirect
        $this->curlRequest('https://lista.mercadolivre.com.br/iphone');
        sleep(1);

        // Passo 3: Tenta aceitar cookies visitando o endpoint de consentimento
        $this->curlRequest('https://www.mercadolivre.com.br/gz/account-verification?go=https%3A%2F%2Flista.mercadolivre.com.br%2Fiphone');
        sleep(1);

        $this->sessionWarmed = true;

        // Verifica se os cookies foram salvos
        $cookieContent = file_exists($this->cookieJar) ? file_get_contents($this->cookieJar) : '';
        $cookieCount = substr_count($cookieContent, "\n") - 4; // Header lines
        $this->progress("  Sessão iniciada ({$cookieCount} cookies)");

        Log::info("[ML Scraper] Sessão aquecida. Cookie jar: {$this->cookieJar}");
    }

    /**
     * Raspa anúncios de um modelo específico.
     */
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

    /**
     * Busca anúncios no Mercado Livre para um modelo+storage.
     */
    private function fetchListings(IphoneModel $model, string $storage): array
    {
        $searchSlug = Str::slug($model->search_term . ' ' . $storage . ' usado');
        $url = self::BASE_URL . '/' . $searchSlug;

        $html = $this->fetchPage($url);

        if (!$html) {
            return [];
        }

        // Verifica se recebemos a página real ou a verificação
        if ($this->isBlockedPage($html)) {
            Log::warning("[ML Scraper] Página de bloqueio/verificação para: {$url}");
            $this->progress("    ⚠ Bloqueado pelo ML para {$model->search_term} {$storage}", 'warn');

            // Tenta redirecionar via URL alternativa
            $html = $this->fetchPageAlternative($model, $storage);
            if (!$html || $this->isBlockedPage($html)) {
                return [];
            }
        }

        return $this->parseJsonLd($html);
    }

    /**
     * Verifica se o HTML recebido é uma página de bloqueio/verificação.
     */
    private function isBlockedPage(string $html): bool
    {
        $indicators = [
            'account-verification',
            'cookie-consent-banner-opt-out',
            'captcha',
            'blocked',
        ];

        $htmlLower = mb_strtolower($html);

        foreach ($indicators as $indicator) {
            if (str_contains($htmlLower, $indicator)) {
                // Confirma: se NÃO tem Product no HTML, é bloqueio
                if (!str_contains($html, '"@type":"Product"') && !str_contains($html, '"price"')) {
                    return true;
                }
            }
        }

        // Se o HTML é muito pequeno para ser uma página de resultados
        if (strlen($html) < 100000 && !str_contains($html, '"price"')) {
            return true;
        }

        return false;
    }

    /**
     * Tenta buscar via URL alternativa (busca direta no www).
     */
    private function fetchPageAlternative(IphoneModel $model, string $storage): ?string
    {
        $query = urlencode($model->search_term . ' ' . $storage . ' usado');
        $url = "https://www.mercadolivre.com.br/jm/search?as_word={$query}";

        return $this->fetchPage($url);
    }

    /**
     * Faz o request HTTP via cURL nativo com cookie jar.
     */
    private function fetchPage(string $url): ?string
    {
        $result = $this->curlRequest($url);

        if (!$result['body']) {
            return null;
        }

        if ($result['http_code'] !== 200) {
            Log::warning("[ML Scraper] HTTP {$result['http_code']} para {$url}");
            return null;
        }

        if (strlen($result['body']) < 1000) {
            Log::warning("[ML Scraper] Resposta muito pequena para {$url} (" . strlen($result['body']) . " bytes)");
            return null;
        }

        return $result['body'];
    }

    /**
     * Executa uma requisição cURL com cookies persistentes.
     */
    private function curlRequest(string $url): array
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
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Sec-Ch-Ua: "Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: none',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
                'Referer: https://www.mercadolivre.com.br/',
            ],
        ];

        // Usa cookie jar se disponível
        if ($this->cookieJar) {
            $options[CURLOPT_COOKIEJAR] = $this->cookieJar;
            $options[CURLOPT_COOKIEFILE] = $this->cookieJar;
        }

        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::warning("[ML Scraper] cURL error para {$url}: {$error}");
            return ['body' => null, 'http_code' => 0, 'effective_url' => $url];
        }

        // Log redirect se aconteceu
        if ($effectiveUrl !== $url) {
            Log::info("[ML Scraper] Redirect: {$url} → {$effectiveUrl}");
        }

        return [
            'body' => $body ?: null,
            'http_code' => $httpCode,
            'effective_url' => $effectiveUrl,
        ];
    }

    /**
     * Extrai produtos do JSON-LD (schema.org) embutido no HTML.
     */
    private function parseJsonLd(string $html): array
    {
        $listings = [];

        if (!preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $matches)) {
            if (!preg_match('/<script[^>]*>(\{["\']@context["\']:["\']https?:..schema\.org.*?)<\/script>/s', $html, $fallback)) {
                return $this->parseFromPricePattern($html);
            }
            $matches[1] = [$fallback[1]];
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

    /**
     * Fallback: extrai preços via regex no HTML.
     */
    private function parseFromPricePattern(string $html): array
    {
        $listings = [];

        // Padrão 1: JSON com "price" (comum em SPAs)
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

        // Padrão 2: Preço exibido no HTML (R$ X.XXX)
        if (empty($listings)) {
            if (preg_match_all('/R\$\s*([\d.]+(?:,\d{2})?)/', $html, $matches)) {
                foreach ($matches[1] as $priceStr) {
                    $clean = str_replace('.', '', $priceStr);
                    $clean = str_replace(',', '.', $clean);
                    $priceFloat = (float) $clean;

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
        }

        return $listings;
    }

    /**
     * Filtra listagens que realmente correspondem ao modelo+storage buscado.
     */
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
