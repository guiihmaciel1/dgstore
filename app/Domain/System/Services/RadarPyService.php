<?php

declare(strict_types=1);

namespace App\Domain\System\Services;

use App\Domain\AI\Services\GeminiService;
use App\Domain\System\Models\RadarProduct;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class RadarPyService
{
    private const CACHE_KEY = 'radar_py_prices';

    private const CACHE_TTL_MINUTES = 120;

    private const MAX_OFFERS = 10;

    public function __construct(
        private readonly GeminiService $geminiService,
    ) {}

    /**
     * Busca preços de todos os produtos ativos e cacheia.
     */
    public function fetchAllProducts(): void
    {
        $products = RadarProduct::active()->get();

        if ($products->isEmpty()) {
            return;
        }

        $results = [];

        foreach ($products as $product) {
            try {
                $offers = $this->fetchPrices($product);

                if (! empty($offers)) {
                    $results[$product->id] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'url'          => $product->url,
                        'offers'       => $offers,
                        'fetched_at'   => now()->format('d/m/Y H:i'),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("RadarPY: falha ao buscar preços para [{$product->name}]", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (! empty($results)) {
            Cache::put(self::CACHE_KEY, $results, now()->addMinutes(self::CACHE_TTL_MINUTES));
        }
    }

    /**
     * Retorna os dados cacheados do Radar PY.
     */
    public function getCached(): array
    {
        return Cache::get(self::CACHE_KEY, []);
    }

    /**
     * Faz scraping de um produto e usa Gemini para extrair ofertas.
     */
    public function fetchPrices(RadarProduct $product): array
    {
        $html = $this->fetchHtml($product->url);

        if ($html === null) {
            return [];
        }

        return $this->extractOffers($html);
    }

    private function fetchHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning("RadarPY: HTTP {$response->status()} ao acessar {$url}");

                return null;
            }

            return $response->body();
        } catch (\Throwable $e) {
            Log::error('RadarPY: falha HTTP', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Extrai ofertas direto do HTML via DomCrawler.
     *
     * Estrutura do Compras Paraguai:
     *  .promocao-produtos-item-box          → container de cada oferta
     *    .promocao-item-nome a              → nome do produto
     *    .promocao-item-preco-oferta strong → preço em USD (ex: "US$ 1.220,00")
     *    .promocao-item-preco-text          → preço em BRL (ex: "R$ 6.344,00")
     *    img.store-image[alt]               → nome da loja
     *    gtag 'advertiser'                  → fallback nome da loja
     */
    private function extractOffersDom(string $html): array
    {
        $crawler = new Crawler($html);
        $offers = [];

        $boxes = $crawler->filter('.promocao-produtos-item-box');

        if ($boxes->count() === 0) {
            return [];
        }

        $boxes->each(function (Crawler $box) use (&$offers) {
            if (count($offers) >= self::MAX_OFFERS) {
                return;
            }

            // Nome do produto
            $productName = null;
            try {
                $nameNode = $box->filter('.promocao-item-nome a');
                if ($nameNode->count() > 0) {
                    $productName = trim($nameNode->first()->text(''));
                }
            } catch (\Throwable) {
            }

            // Preço USD
            $priceUsd = null;
            try {
                $usdNode = $box->filter('.promocao-item-preco-oferta strong');
                if ($usdNode->count() > 0) {
                    $usdText = trim($usdNode->first()->text(''));
                    if (preg_match('/[\d.,]+/', $usdText, $m)) {
                        $priceUsd = $this->parsePrice($m[0]);
                    }
                }
            } catch (\Throwable) {
            }

            // Preço BRL
            $priceBrl = null;
            try {
                $brlNode = $box->filter('.promocao-item-preco-text');
                if ($brlNode->count() > 0) {
                    $brlText = trim($brlNode->first()->text(''));
                    if (preg_match('/[\d.,]+/', $brlText, $m)) {
                        $priceBrl = $this->parsePrice($m[0]);
                    }
                }
            } catch (\Throwable) {
            }

            // Nome da loja via img.store-image alt
            $storeName = null;
            try {
                $storeImg = $box->filter('img.store-image');
                if ($storeImg->count() > 0) {
                    $storeName = trim($storeImg->first()->attr('alt') ?? '')
                              ?: trim($storeImg->first()->attr('title') ?? '');
                }
            } catch (\Throwable) {
            }

            // Fallback: 'advertiser' nos eventos gtag() inline
            if (! $storeName) {
                $boxHtml = $box->outerHtml();
                if (preg_match("/'advertiser':\s*'([^']+)'/", $boxHtml, $m)) {
                    $storeName = html_entity_decode(trim($m[1]));
                }
            }

            if ($priceUsd && $priceUsd > 0) {
                $offers[] = [
                    'product_name' => $productName ?: 'Produto',
                    'price_usd'    => $priceUsd,
                    'price_brl'    => $priceBrl ?? round($priceUsd * 5.20, 2),
                    'store_name'   => $storeName ?: null,
                ];
            }
        });

        return $offers;
    }

    private function parsePrice(string $raw): float
    {
        // "1.220,00" → 1220.00 / "1220.00" → 1220.00
        $clean = trim($raw);

        if (str_contains($clean, ',')) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        }

        return round((float) $clean, 2);
    }

    /**
     * Fallback: usa Gemini para extrair ofertas quando o DOM não encontra a classe esperada.
     */
    private function extractOffersGeminiFallback(string $html): array
    {
        if (! $this->geminiService->isAvailable()) {
            return [];
        }

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $text = mb_substr(trim($text), 0, 15000);

        $prompt = <<<PROMPT
Extraia as 10 primeiras ofertas de produto desta página de comparação de preços do Compras Paraguai.

Para cada oferta retorne:
- "product_name": nome do anúncio
- "price_usd": preço em dólar (número)
- "price_brl": preço em reais (número)
- "store_name": nome da loja

Dados:
{$text}
PROMPT;

        $result = $this->geminiService->generateJson($prompt, 'Parser de e-commerce. Retorne apenas array JSON.');

        if (! is_array($result)) {
            return [];
        }

        $offers = isset($result[0]) ? $result : ($result['offers'] ?? []);

        return array_slice(
            array_filter($offers, fn ($o) => isset($o['price_usd']) && $o['price_usd'] > 0),
            0,
            self::MAX_OFFERS
        );
    }

    private function extractOffers(string $html): array
    {
        $offers = $this->extractOffersDom($html);

        if (! empty($offers)) {
            return $offers;
        }

        Log::info('RadarPY: DOM extraction vazia, usando Gemini como fallback');

        return $this->extractOffersGeminiFallback($html);
    }
}
