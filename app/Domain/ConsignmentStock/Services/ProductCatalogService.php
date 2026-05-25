<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Services;

use App\Domain\ConsignmentStock\Config\StandardColors;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Product\Models\Product;
use App\Domain\Valuation\Models\IphoneModel;
use Illuminate\Support\Collection;

/**
 * Servico que unifica o catalogo de produtos para autocomplete na entrada
 * de estoque consignado.
 *
 * Fontes:
 *  - iphone_models     (cores oficiais por modelo de iPhone)
 *  - products          (lineup Apple completo - iPad, Mac, Watch, AirPods)
 *  - consignment_stock_items  (historico real do que ja passou pelo estoque)
 */
class ProductCatalogService
{
    public function searchByTerm(string $term, int $limit = 20): array
    {
        $term = trim($term);
        $catalog = $this->buildCatalog();

        if ($term === '') {
            return $catalog->take($limit)->values()->all();
        }

        $needle = mb_strtolower($term);

        return $catalog
            ->filter(fn (array $item) => str_contains(mb_strtolower($item['name']), $needle))
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Constroi o catalogo unificado, com deduplicacao por nome (case-insensitive).
     *
     * @return Collection<int, array{name: string, storages: array, colors: array, category: string, release_year: int|null, in_stock_count: int}>
     */
    private function buildCatalog(): Collection
    {
        $byKey = [];

        $this->mergeFromIphoneModels($byKey);
        $this->mergeFromProducts($byKey);
        $this->mergeFromConsignmentHistory($byKey);

        // Aplica cores padronizadas para modelos específicos
        $this->applyStandardColors($byKey);

        return collect($byKey)
            ->sortByDesc(fn (array $item) => $item['release_year'] ?? 0)
            ->values();
    }

    /**
     * @param array<string, array> $byKey
     */
    private function mergeFromIphoneModels(array &$byKey): void
    {
        IphoneModel::active()->get()->each(function (IphoneModel $model) use (&$byKey) {
            $name = $this->normalizeProductName($model->name);
            $key = $this->key($name);

            $byKey[$key] = [
                'name' => $name,
                'storages' => array_values((array) $model->storages),
                'colors' => array_values((array) $model->colors),
                'category' => 'smartphone',
                'release_year' => $model->release_year,
                'in_stock_count' => 0,
            ];
        });
    }

    /**
     * @param array<string, array> $byKey
     */
    private function mergeFromProducts(array &$byKey): void
    {
        // Agrupa produtos por nome base (sem storage no final), pois o seeder cria 1 SKU por storage
        Product::active()
            ->where('supplier', 'Apple')
            ->get()
            ->each(function (Product $product) use (&$byKey) {
                $baseName = $this->normalizeProductName($this->stripStorageFromName($product->name));
                $key = $this->key($baseName);

                if (!isset($byKey[$key])) {
                    $byKey[$key] = [
                        'name' => $baseName,
                        'storages' => [],
                        'colors' => [],
                        'category' => $product->category?->value ?? 'smartphone',
                        'release_year' => null,
                        'in_stock_count' => 0,
                    ];
                }

                if ($product->storage && !in_array($product->storage, $byKey[$key]['storages'], true)) {
                    $byKey[$key]['storages'][] = $product->storage;
                }
            });
    }

    /**
     * @param array<string, array> $byKey
     */
    private function mergeFromConsignmentHistory(array &$byKey): void
    {
        ConsignmentStockItem::query()
            ->selectRaw('name, model, storage, color, COUNT(*) as items_count, SUM(available_quantity) as available_total')
            ->groupBy('name', 'model', 'storage', 'color')
            ->get()
            ->each(function ($row) use (&$byKey) {
                $canonicalName = $this->normalizeProductName($row->name);
                $key = $this->key($canonicalName);

                if (!isset($byKey[$key])) {
                    $byKey[$key] = [
                        'name' => $canonicalName,
                        'storages' => [],
                        'colors' => [],
                        'category' => 'smartphone',
                        'release_year' => null,
                        'in_stock_count' => 0,
                    ];
                }

                if ($row->storage && !in_array($row->storage, $byKey[$key]['storages'], true)) {
                    $byKey[$key]['storages'][] = $row->storage;
                }

                if ($row->color && !in_array($row->color, $byKey[$key]['colors'], true)) {
                    $byKey[$key]['colors'][] = $row->color;
                }

                $byKey[$key]['in_stock_count'] += (int) $row->available_total;
            });
    }

    /**
     * Aplica cores padronizadas para modelos específicos.
     * 
     * Sobrescreve o array de cores com as cores oficiais definidas em StandardColors
     * para garantir consistência nos registros de estoque.
     * 
     * @param array<string, array> $byKey
     */
    private function applyStandardColors(array &$byKey): void
    {
        foreach ($byKey as $key => &$item) {
            $standardColors = StandardColors::getColorsForModel($item['name']);
            
            if ($standardColors !== null) {
                $item['colors'] = $standardColors;
            }
        }
    }

    /**
     * Remove o sufixo de storage que o AppleProductSeeder adiciona (ex.: "256GB", "1TB").
     */
    private function stripStorageFromName(string $name): string
    {
        return trim((string) preg_replace('/\s+\d+(GB|TB)$/i', '', $name));
    }

    /**
     * Normaliza nomes abreviados para o formato canonico (ex: "17 Pro Max" -> "iPhone 17 Pro Max").
     */
    private function normalizeProductName(string $name): string
    {
        $name = trim($name);

        if (preg_match('/^iPhone\s+/i', $name)) {
            return $name;
        }

        // Atalhos comuns sem prefixo "iPhone" (ex: "17 Pro Max", "17 Pro", "17")
        if (preg_match('/^(\d+(?:\s+(?:Pro\s+Max|Pro|Plus|mini|Air|e))?)$/i', $name)) {
            return 'iPhone ' . $name;
        }

        return $name;
    }

    private function key(string $name): string
    {
        return mb_strtolower($this->normalizeProductName($name));
    }
}
