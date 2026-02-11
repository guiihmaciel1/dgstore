<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OlxScraperService
{
    /**
     * URL base do OLX para iPhones em São José do Rio Preto - SP.
     */
    private const BASE_URL = 'https://www.olx.com.br/eletronicos-e-celulares/celulares/iphone/estado-sp/regiao-de-sao-jose-do-rio-preto/sao-jose-do-rio-preto';

    /**
     * Pool de User-Agents reais para rotação.
     */
    private const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:121.0) Gecko/20100101 Firefox/121.0',
    ];

    /**
     * Delay mínimo e máximo entre requests (segundos).
     */
    private const MIN_DELAY = 2;
    private const MAX_DELAY = 5;

    /**
     * Máximo de tentativas por request.
     */
    private const MAX_RETRIES = 2;

    /**
     * Executa o scraping para todos os modelos ativos.
     *
     * @return array Resumo da execução (total de listings coletados e erros)
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

                Log::info("[OLX Scraper] {$model->name}: {$count} anúncios coletados.");
            } catch (\Throwable $e) {
                $errors[] = "{$model->name}: {$e->getMessage()}";
                Log::error("[OLX Scraper] Erro ao raspar {$model->name}: {$e->getMessage()}");
            }

            // Delay entre modelos
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
     * Raspa os anúncios de um modelo específico.
     */
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

            // Delay entre variantes de storage
            if (count($model->storages) > 1) {
                $this->randomDelay();
            }
        }

        return $totalCount;
    }

    /**
     * Faz o request HTTP e extrai os anúncios do HTML.
     *
     * @return array Lista de anúncios extraídos
     */
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
     * Faz o request HTTP com retry e backoff.
     */
    private function fetchWithRetry(string $url): ?string
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => $this->randomUserAgent(),
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Cache-Control' => 'no-cache',
                    'Connection' => 'keep-alive',
                ])->connectTimeout(5)->timeout(10)->get($url);

                if ($response->successful()) {
                    return $response->body();
                }

                Log::warning("[OLX Scraper] HTTP {$response->status()} para {$url} (tentativa {$attempt})");
            } catch (\Throwable $e) {
                Log::warning("[OLX Scraper] Erro na tentativa {$attempt} para {$url}: {$e->getMessage()}");
            }

            // Backoff exponencial entre tentativas
            if ($attempt < self::MAX_RETRIES) {
                sleep($attempt * 3);
            }
        }

        return null;
    }

    /**
     * Extrai anúncios do HTML da página do OLX.
     *
     * O OLX usa Next.js e embute os dados no script __NEXT_DATA__.
     *
     * @return array Lista de anúncios com título, preço, url e localização
     */
    private function parseListings(string $html): array
    {
        $listings = [];

        // Tenta extrair via __NEXT_DATA__ (método principal)
        $listings = $this->parseFromNextData($html);

        // Fallback: extrair via regex no HTML renderizado
        if (empty($listings)) {
            $listings = $this->parseFromHtml($html);
        }

        return $listings;
    }

    /**
     * Extrai anúncios do JSON __NEXT_DATA__ embutido no HTML.
     */
    private function parseFromNextData(string $html): array
    {
        if (!preg_match('/<script\s+id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $matches)) {
            return [];
        }

        $data = json_decode($matches[1], true);

        if (!$data) {
            Log::warning('[OLX Scraper] Falha ao decodificar __NEXT_DATA__ JSON.');
            return [];
        }

        // Navega pela estrutura do JSON para encontrar os anúncios
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

    /**
     * Navega pela estrutura do __NEXT_DATA__ para encontrar o array de anúncios.
     * A estrutura pode variar, então tentamos múltiplos caminhos.
     */
    private function extractAdsFromNextData(array $data): array
    {
        // Caminho comum: props.pageProps.ads
        $ads = $data['props']['pageProps']['ads'] ?? null;
        if (is_array($ads) && !empty($ads)) {
            return $ads;
        }

        // Caminho alternativo: props.pageProps.searchResult.ads
        $ads = $data['props']['pageProps']['searchResult']['ads'] ?? null;
        if (is_array($ads) && !empty($ads)) {
            return $ads;
        }

        // Caminho alternativo: props.pageProps.adList
        $ads = $data['props']['pageProps']['adList'] ?? null;
        if (is_array($ads) && !empty($ads)) {
            return $ads;
        }

        // Busca recursiva por array com chave 'price' ou 'title'
        return $this->findAdsRecursive($data, 0);
    }

    /**
     * Busca recursiva pelo array de anúncios no JSON.
     */
    private function findAdsRecursive(array $data, int $depth): array
    {
        if ($depth > 5) {
            return [];
        }

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            // Verifica se é uma lista de anúncios (array de arrays com 'price' ou 'title')
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

    /**
     * Extrai o preço de um anúncio do OLX.
     */
    private function extractPrice(array $ad): float
    {
        // Formato: "price" como string "R$ 6.500" ou number
        if (isset($ad['price'])) {
            if (is_numeric($ad['price'])) {
                return (float) $ad['price'];
            }

            if (is_string($ad['price'])) {
                return $this->parsePrice($ad['price']);
            }

            // Pode ser um objeto: { "value": 6500, "formattedValue": "R$ 6.500" }
            if (is_array($ad['price'])) {
                if (isset($ad['price']['value'])) {
                    return (float) $ad['price']['value'];
                }
                if (isset($ad['price']['formattedValue'])) {
                    return $this->parsePrice($ad['price']['formattedValue']);
                }
            }
        }

        // Formato alternativo: "priceValue"
        if (isset($ad['priceValue']) && is_numeric($ad['priceValue'])) {
            return (float) $ad['priceValue'];
        }

        return 0.0;
    }

    /**
     * Converte string de preço brasileiro para float.
     * Ex: "R$ 6.500" => 6500.0, "R$ 6.500,00" => 6500.0
     */
    private function parsePrice(string $priceString): float
    {
        // Remove "R$", espaços e outros caracteres
        $clean = preg_replace('/[^\d.,]/', '', $priceString);

        // Se tem vírgula como separador decimal
        if (str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } else {
            // Se não tem vírgula, remove pontos de milhar
            // Verifica se o ponto é separador de milhar (ex: 6.500)
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $clean)) {
                $clean = str_replace('.', '', $clean);
            }
        }

        return (float) $clean;
    }

    /**
     * Extrai a localização de um anúncio.
     */
    private function extractLocation(array $ad): ?string
    {
        if (isset($ad['location'])) {
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
        }

        return null;
    }

    /**
     * Fallback: extrai anúncios parseando o HTML renderizado.
     */
    private function parseFromHtml(string $html): array
    {
        $listings = [];

        // Padrão para encontrar preços no HTML do OLX
        // Os anúncios geralmente estão em cards com preço e título
        if (preg_match_all('/data-ds-component="DS-AdCard".*?<\/a>/s', $html, $cardMatches)) {
            foreach ($cardMatches[0] as $card) {
                $title = '';
                $price = 0.0;
                $url = null;

                // Extrai título
                if (preg_match('/aria-label="([^"]+)"/', $card, $titleMatch)) {
                    $title = html_entity_decode($titleMatch[1]);
                }

                // Extrai preço
                if (preg_match('/R\$\s*([\d.,]+)/', $card, $priceMatch)) {
                    $price = $this->parsePrice($priceMatch[0]);
                }

                // Extrai URL
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

    /**
     * Salva um anúncio coletado no banco de dados.
     */
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

    /**
     * Delay aleatório entre requests.
     */
    private function randomDelay(): void
    {
        sleep(rand(self::MIN_DELAY, self::MAX_DELAY));
    }

    /**
     * Retorna um User-Agent aleatório do pool.
     */
    private function randomUserAgent(): string
    {
        return self::USER_AGENTS[array_rand(self::USER_AGENTS)];
    }
}
