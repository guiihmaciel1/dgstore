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

        return $this->extractOffersWithGemini($html);
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
     * Pré-extrai ofertas do HTML usando DomCrawler para estruturar
     * os dados antes de enviar ao Gemini (mais preciso e econômico em tokens).
     */
    private function preExtractOffers(string $html): string
    {
        $crawler = new Crawler($html);
        $rawOffers = [];

        // Cada oferta no Compras Paraguai é um bloco dentro da lista de resultados.
        // Extraímos o texto completo de cada bloco de oferta.
        $blocks = $crawler->filter('.product-list-item, .offer-item, [class*="product-card"], [class*="offer"]');

        if ($blocks->count() > 0) {
            $blocks->each(function (Crawler $block) use (&$rawOffers) {
                if (count($rawOffers) >= 15) {
                    return;
                }

                $text = trim(preg_replace('/\s+/', ' ', $block->text('')) ?? '');
                // Pegar alt de imagens (pode conter nome da loja)
                $imgs = [];
                $block->filter('img')->each(function (Crawler $img) use (&$imgs) {
                    $alt = trim($img->attr('alt') ?? '');
                    if ($alt !== '' && mb_strlen($alt) < 100) {
                        $imgs[] = $alt;
                    }
                });

                if ($text !== '' && mb_strlen($text) > 10) {
                    $rawOffers[] = $text . (! empty($imgs) ? ' [imgs: ' . implode(', ', $imgs) . ']' : '');
                }
            });
        }

        // Fallback: extrair por regex com captura de alt das imagens de logo
        if (empty($rawOffers)) {
            // Lojas aparecem como img com alt dentro de links de loja ou próximas ao preço
            $allImgAlts = [];
            $crawler->filter('img[alt]')->each(function (Crawler $img) use (&$allImgAlts) {
                $alt = trim($img->attr('alt') ?? '');
                $src = strtolower($img->attr('src') ?? '');
                // Filtrar: logos de loja geralmente têm "logo" no src ou são nomes curtos sem palavras de produto
                $isProduct = preg_match('/iphone|apple|celular|smartphone|produto|banner/i', $alt);
                $isLikely = str_contains($src, 'logo') || str_contains($src, 'loja') || str_contains($src, 'store');

                if ($alt !== '' && mb_strlen($alt) < 60 && (! $isProduct || $isLikely)) {
                    $allImgAlts[] = $alt;
                }
            });

            $text = strip_tags($html);
            $text = preg_replace('/\s+/', ' ', $text) ?? $text;

            if (preg_match_all('/([^.]{10,100}?)\s+(?:Código:\s*\S+\s+)?US\$\s*([\d.,]+)\s+R\$\s*([\d.,]+)/u', $text, $matches, PREG_SET_ORDER)) {
                foreach (array_slice($matches, 0, 15) as $i => $m) {
                    $storeName = $allImgAlts[$i] ?? '';
                    $rawOffers[] = trim($m[0]) . ($storeName ? " [loja: {$storeName}]" : '');
                }
            }
        }

        if (empty($rawOffers)) {
            // Último fallback: mandar HTML limpo truncado
            $clean = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
            $clean = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $clean) ?? $clean;
            $clean = strip_tags($clean);
            $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;

            return mb_substr(trim($clean), 0, 15000);
        }

        return "OFERTAS ENCONTRADAS:\n\n" . implode("\n---\n", $rawOffers);
    }

    private function extractOffersWithGemini(string $html): array
    {
        if (! $this->geminiService->isAvailable()) {
            Log::warning('RadarPY: Gemini não disponível');

            return [];
        }

        $extractedData = $this->preExtractOffers($html);

        $prompt = <<<PROMPT
Analise estes dados extraídos de uma página de comparação de preços do Compras Paraguai.
A página lista ofertas de um mesmo produto em diferentes lojas do Paraguai, ordenadas por menor preço.

Extraia as primeiras 10 ofertas DISTINTAS (de lojas diferentes, se possível).

Para cada oferta, retorne:
- "product_name": nome/descrição do anúncio (ex: "iPhone 17 Pro Max 256GB Orange")
- "price_usd": preço em dólar americano como número (ex: 1220.00)  
- "price_brl": preço em reais como número (ex: 6344.00)
- "store_name": nome da loja que vende. Os nomes de loja geralmente aparecem nas tags [imgs:] ou próximos aos preços. Exemplos: "Mundo Celular", "Tele Con Cell", "Star Midia", "Prime", "Mega Eletronicos", etc.

IMPORTANTE: Cada oferta deve ser DIFERENTE (preço ou loja diferente). Não repita a mesma oferta.

Dados:
{$extractedData}
PROMPT;

        $result = $this->geminiService->generateJson(
            $prompt,
            'Você é um parser de dados de e-commerce. Extraia ofertas distintas com preços e lojas. Retorne apenas array JSON.'
        );

        if ($result === null) {
            Log::warning('RadarPY: Gemini não retornou dados válidos');

            return [];
        }

        $offers = is_array($result) && isset($result[0]) ? $result : ($result['offers'] ?? $result);

        if (! is_array($offers)) {
            return [];
        }

        $filtered = array_filter($offers, fn ($o) => isset($o['price_usd']) && $o['price_usd'] > 0);

        return array_slice(array_map(function ($o) {
            $store = $o['store_name'] ?? '';
            if ($store === '' || strtolower($store) === 'unknown' || strtolower($store) === 'loja não identificada') {
                $o['store_name'] = null;
            }

            return $o;
        }, $filtered), 0, self::MAX_OFFERS);
    }
}
