<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Services;

use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Support\Collection;

/**
 * Compara a tabela de Novos (MarketingPrice) com a última cotação
 * do fornecedor local Santa Fé recebida via WhatsApp.
 */
class LocalSupplierComparisonService
{
    private const SUPPLIER_NAME_LIKE = '%Santa F%';

    private const WHATSAPP_NOTES_LIKE = 'Importado via WhatsApp%';

    private const IPHONE_MODEL_PATTERN = '/^\d{2}[a-z]?(?: (?:pro max|pro|plus|air|mini))?$/';

    /**
     * Cor escrita pelo fornecedor => nomes equivalentes em MarketingPrice::COLORS (minúsculo).
     */
    public const SUPPLIER_TO_OUR_COLOR = [
        'preto' => ['preto', 'meia-noite'],
        'black' => ['preto', 'meia-noite'],
        'midnight' => ['meia-noite', 'preto'],
        'midnigth' => ['meia-noite', 'preto'],
        'branco' => ['prata', 'branco', 'estelar', 'titânio branco'],
        'white' => ['prata', 'branco', 'estelar', 'titânio branco'],
        'silver' => ['prata', 'branco'],
        'bordo' => ['bordô'],
        'azul' => ['glacial', 'azul', 'ultramarino'],
        'blue' => ['glacial', 'azul', 'ultramarino'],
        'mist blue' => ['glacial', 'azul'],
        'laranja' => ['laranja', 'deserto', 'titânio natural'],
        'lavender' => ['lavanda', 'lavender', 'roxo'],
        'sage' => ['sage', 'verde'],
    ];

    /**
     * @param Collection<int, MarketingPrice> $prices
     */
    public function compare(Collection $prices): ?array
    {
        $supplier = Supplier::active()->where('name', 'like', self::SUPPLIER_NAME_LIKE)->first();
        if (!$supplier) {
            return null;
        }

        $whatsappQuotes = fn () => Quotation::where('supplier_id', $supplier->id)
            ->where('notes', 'like', self::WHATSAPP_NOTES_LIKE);

        $latestDate = $whatsappQuotes()->max('quoted_at');
        if (!$latestDate) {
            return null;
        }

        $quoteDate = substr((string) $latestDate, 0, 10);
        $quotes = $whatsappQuotes()
            ->whereDate('quoted_at', $quoteDate)
            ->where('unit_price', '>', 0)
            ->get(['product_name', 'unit_price']);

        $supplierByKey = $this->groupSupplierQuotes($quotes);
        [$byPriceId, $matchedVariants] = $this->matchPrices($prices, $supplierByKey);

        return [
            'supplier_name' => $supplier->name,
            'quoted_at' => $quoteDate,
            'items_count' => $quotes->count(),
            'by_price_id' => $byPriceId,
            'missing_in_marketing' => $this->buildMissing($supplierByKey, $matchedVariants),
        ];
    }

    /**
     * Agrupa as variantes de iPhone por "modelo|storage".
     *
     * @return array<string, array{model: string, storage: string, variants: array<int, array{color: string, price: float}>}>
     */
    private function groupSupplierQuotes(Collection $quotes): array
    {
        $grouped = [];

        foreach ($quotes as $quote) {
            $parts = preg_split('/\s-\s/', (string) $quote->product_name, 2);
            $parsed = $this->parseModelStorage($parts[0]);
            if (!$parsed) {
                continue;
            }

            $key = $parsed['model'] . '|' . $parsed['storage'];
            $grouped[$key] ??= ['model' => $parsed['model'], 'storage' => $parsed['storage'], 'variants' => []];
            $grouped[$key]['variants'][] = [
                'color' => isset($parts[1]) ? trim($parts[1]) : '',
                'price' => (float) $quote->unit_price,
            ];
        }

        return $grouped;
    }

    private function matchPrices(Collection $prices, array $supplierByKey): array
    {
        $byPriceId = [];
        $matchedVariants = [];

        foreach ($prices as $price) {
            if (($price->category ?? 'iphone') !== 'iphone' || $price->isOnOrder()) {
                continue;
            }

            $parsed = $this->parseModelStorage((string) $price->name, (string) $price->storage);
            $key = $parsed ? $parsed['model'] . '|' . $parsed['storage'] : null;
            $group = $key ? ($supplierByKey[$key] ?? null) : null;

            if (!$group) {
                $byPriceId[$price->id] = $this->result('none');
                continue;
            }

            $modelMin = min(array_column($group['variants'], 'price'));
            $ourColor = $this->resolveOurColor($price);

            // Linha inativa não "cobre" a variante: ela continua em "fornecedor tem, nós não".
            $coversVariants = (bool) $price->active;

            if ($ourColor === null) {
                if ($coversVariants) {
                    foreach (array_keys($group['variants']) as $idx) {
                        $matchedVariants[$key][$idx] = true;
                    }
                }
                $byPriceId[$price->id] = $this->result('no_color', $modelMin, null, $modelMin, $group['variants']);
                continue;
            }

            $variantIdx = $this->findVariantForColor($group['variants'], $ourColor);
            if ($variantIdx === null) {
                $byPriceId[$price->id] = $this->result('color_missing', null, null, $modelMin, $group['variants']);
                continue;
            }

            $variant = $group['variants'][$variantIdx];
            if ($coversVariants) {
                $matchedVariants[$key][$variantIdx] = true;
            }
            $byPriceId[$price->id] = $this->result('color', $variant['price'], $variant['color'], $modelMin, $group['variants']);
        }

        return [$byPriceId, $matchedVariants];
    }

    private function result(string $type, ?float $cost = null, ?string $color = null, ?float $modelMin = null, array $variants = []): array
    {
        return [
            'type' => $type,
            'supplier_cost' => $cost,
            'supplier_color' => $color,
            'model_min' => $modelMin,
            'variants' => $variants,
        ];
    }

    private function findVariantForColor(array $variants, string $ourColor): ?int
    {
        foreach ($variants as $idx => $variant) {
            $supplierColor = mb_strtolower($variant['color']);
            $candidates = self::SUPPLIER_TO_OUR_COLOR[$supplierColor] ?? [$supplierColor];
            if (in_array($ourColor, $candidates, true)) {
                return $idx;
            }
        }

        return null;
    }

    private function buildMissing(array $supplierByKey, array $matchedVariants): array
    {
        $missing = [];

        foreach ($supplierByKey as $key => $group) {
            foreach ($group['variants'] as $idx => $variant) {
                if (isset($matchedVariants[$key][$idx])) {
                    continue;
                }

                [$colorName, $colorHex] = $this->suggestOurColor($variant['color']);
                $missing[] = [
                    'name' => 'iPhone ' . ucwords($group['model']),
                    'storage' => mb_strtoupper($group['storage']),
                    'supplier_color' => $variant['color'],
                    'color' => $colorName,
                    'color_hex' => $colorHex,
                    'price' => $variant['price'],
                ];
            }
        }

        usort($missing, fn ($a, $b) => $b['price'] <=> $a['price']);

        return $missing;
    }

    /**
     * Extrai "modelo" e "storage" normalizados de um nome de iPhone.
     * Ex.: "iPhone 17 Pro Max" + "512GB" => ['model' => '17 pro max', 'storage' => '512gb'].
     */
    private function parseModelStorage(string $name, string $storage = ''): ?array
    {
        $text = mb_strtolower($name);
        $text = preg_replace('/\(.*?\)/u', ' ', $text);

        $storageNorm = $this->normalizeStorage($storage);
        if (preg_match('/(\d+)\s*(gb|tb)\b/u', $text, $m)) {
            $storageNorm ??= $m[1] . $m[2];
            $text = str_replace($m[0], ' ', $text);
        }

        $text = preg_replace('/\biphone\b/u', ' ', $text);
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($storageNorm === null || !preg_match(self::IPHONE_MODEL_PATTERN, $text)) {
            return null;
        }

        return ['model' => $text, 'storage' => $storageNorm];
    }

    private function normalizeStorage(string $storage): ?string
    {
        if (preg_match('/(\d+)\s*(gb|tb)/i', $storage, $m)) {
            return $m[1] . mb_strtolower($m[2]);
        }

        return null;
    }

    private function resolveOurColor(MarketingPrice $price): ?string
    {
        $color = $price->color ?: (MarketingPrice::COLORS[$price->color_hex ?? ''] ?? null);

        return $color ? mb_strtolower(trim($color)) : null;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function suggestOurColor(string $supplierColor): array
    {
        $candidates = self::SUPPLIER_TO_OUR_COLOR[mb_strtolower($supplierColor)] ?? [];

        foreach ($candidates as $candidate) {
            foreach (MarketingPrice::COLORS as $hex => $label) {
                if (mb_strtolower($label) === $candidate) {
                    return [$label, $hex];
                }
            }
        }

        return ['', ''];
    }
}
