<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Valuation\Services\NegotiationEvaluatorService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NegotiationController extends Controller
{
    public function __construct(
        private readonly NegotiationEvaluatorService $evaluatorService,
    ) {}

    public function index(): View
    {
        $marketingPrices = MarketingPrice::active()->ordered()->get();
        $quickValues = $this->buildQuickValues($marketingPrices);
        $quickValuesEncomenda = $this->buildQuickValues($marketingPrices, 'encomenda_price');
        $usedQuickValues = $this->buildUsedQuickValues();
        $tradeInModels = config('dgifipe.models');

        return view('tools.negotiation-simulator', [
            'quickValuesFromMarketing' => $quickValues,
            'quickValuesEncomenda' => $quickValuesEncomenda,
            'quickValuesUsed' => $usedQuickValues,
            'tradeInModels' => $tradeInModels,
        ]);
    }

    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'storage' => ['required', 'string'],
            'battery_health' => ['required', 'integer', 'min:0', 'max:100'],
            'device_state' => ['required', 'string', 'in:original,repaired'],
            'no_box' => ['boolean'],
            'no_cable' => ['boolean'],
        ]);

        $accessoryChecks = [
            'no_box' => ! empty($validated['no_box']),
            'no_cable' => ! empty($validated['no_cable']),
        ];

        try {
            $result = $this->evaluatorService->evaluate(
                model: $validated['model'],
                storage: $validated['storage'],
                batteryHealth: (int) $validated['battery_health'],
                deviceState: $validated['device_state'],
                accessoryChecks: $accessoryChecks,
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (QueryException $e) {
            Log::error('Falha na avaliação de seminovo (DB)', [
                'error' => $e->getMessage(),
                'model' => $validated['model'],
                'storage' => $validated['storage'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao avaliar seminovo. Verifique a conexão com o banco de dados.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Falha na avaliação de seminovo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'model' => $validated['model'],
                'storage' => $validated['storage'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao avaliar seminovo. Tente novamente em alguns instantes.',
            ], 500);
        }
    }

    private const AVAILABILITY_PATTERN = '/\s*\((pronta entrega|sob encomenda|encomenda)\)\s*/i';

    /**
     * Valores rápidos ordenados por: modelo (ordem do Marketing) → armazenamento (maior primeiro)
     * → Pronta Entrega antes de Encomenda. Cada linha da tela agrupa modelo + armazenamento.
     *
     * @param string $priceField 'price' (pronta entrega) ou 'encomenda_price' (só itens com valor de encomenda)
     */
    private function buildQuickValues(\Illuminate\Support\Collection $prices, string $priceField = 'price'): array
    {
        $colorNames = MarketingPrice::COLORS;
        if ($priceField !== 'price') {
            $prices = $prices->filter(fn ($p) => (float) $p->{$priceField} > 0)->values();
        }

        $modelOrder = [];
        foreach ($prices as $p) {
            $modelOrder[$this->baseModelName($p->name)] ??= count($modelOrder);
        }

        $grouped = $prices->groupBy(function ($p) {
            return trim($p->name . ' ' . ($p->storage ?? ''));
        });

        return $grouped->map(function ($items, $key) use ($colorNames, $modelOrder, $priceField) {
            $first = $items->first();
            $baseModel = $this->baseModelName($first->name);
            $entry = [
                'name' => $key,
                'model_base' => trim($baseModel . ' ' . ($first->storage ?? '')),
                '_sort' => [$modelOrder[$baseModel], -$this->storageInGb($first->storage), $first->isOnOrder() ? 1 : 0],
            ];

            if ($items->count() === 1 && (! $first->color || $first->color === 'Todas')) {
                return $entry + [
                    'value' => (float) $first->{$priceField},
                    'cost_price' => (float) ($first->cost_price ?? 0),
                ];
            }

            return $entry + ['value' => 0, 'variants' => $this->buildGroupedVariants($items, $colorNames, $priceField)];
        })
            ->sort(fn ($a, $b) => $a['_sort'] <=> $b['_sort'])
            ->map(fn ($entry) => array_diff_key($entry, ['_sort' => true]))
            ->values()
            ->toArray();
    }

    private function baseModelName(?string $name): string
    {
        return trim((string) preg_replace(self::AVAILABILITY_PATTERN, ' ', (string) $name));
    }

    private function storageInGb(?string $storage): int
    {
        if (! $storage || ! preg_match('/(\d+)\s*(TB|GB)/i', $storage, $m)) {
            return 0;
        }

        return strtoupper($m[2]) === 'TB' ? (int) $m[1] * 1024 : (int) $m[1];
    }

    /**
     * Agrupa variantes de cor com mesmo preço em "Cor A | Cor B".
     */
    private function buildGroupedVariants(\Illuminate\Support\Collection $items, array $colorNames, string $priceField = 'price'): array
    {
        $resolved = $items->map(function ($item) use ($colorNames, $priceField) {
            $colorLabel = $item->color;
            if (!$colorLabel && $item->color_hex) {
                $colorLabel = $colorNames[$item->color_hex] ?? null;
            }

            return [
                'label' => $colorLabel ?: 'Sem cor',
                'color' => $item->color_hex ?: '#cccccc',
                'value' => (float) $item->{$priceField},
                'cost_price' => (float) ($item->cost_price ?? 0),
            ];
        });

        $byPrice = $resolved->groupBy('value');

        return $byPrice->map(function ($group) {
            $labels = $group->pluck('label')->unique()->values()->toArray();
            $first = $group->first();

            return [
                'label' => implode(' | ', $labels),
                'color' => $first['color'],
                'value' => $first['value'],
                'cost_price' => $first['cost_price'],
                'colors' => $group->map(fn ($v) => ['label' => $v['label'], 'hex' => $v['color']])->unique('label')->values()->toArray(),
            ];
        })->sortByDesc('value')->values()->toArray();
    }

    private function buildUsedQuickValues(): array
    {
        $products = Product::with('openRepair')
            ->where('active', true)
            ->where('stock_quantity', '>', 0)
            ->whereIn('condition', [ProductCondition::Used, ProductCondition::Refurbished])
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->orderBy('name')
            ->get();

        return $products->map(function (Product $product) {
            $parts = array_filter([$product->name, $product->storage]);
            $label = implode(' ', $parts);

            if ($product->color) {
                $label .= ' ' . $product->color;
            }

            return [
                'name' => $label,
                'value' => (float) $product->sale_price,
                'cost_price' => (float) ($product->final_cost ?? 0),
                'battery_health' => $product->battery_health,
                'has_box' => (bool) $product->has_box,
                'has_cable' => (bool) $product->has_cable,
                'notes' => $product->notes,
                'is_used' => true,
                'in_repair' => $product->isInRepair(),
            ];
        })->values()->toArray();
    }
}
