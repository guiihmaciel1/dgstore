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
        $quickValues = $this->buildQuickValues();
        $usedQuickValues = $this->buildUsedQuickValues();
        $tradeInModels = config('dgifipe.models');

        return view('tools.negotiation-simulator', [
            'quickValuesFromMarketing' => $quickValues,
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

    private function buildQuickValues(): array
    {
        $colorNames = MarketingPrice::COLORS;
        $prices = MarketingPrice::active()->ordered()->get();

        $grouped = $prices->groupBy(function ($p) {
            return trim($p->name . ' ' . ($p->storage ?? ''));
        });

        return $grouped->map(function ($items, $key) use ($colorNames) {
            $modelBase = trim($items->first()->name ?? '');

            if ($items->count() === 1 && (! $items->first()->color || $items->first()->color === 'Todas')) {
                return [
                    'name' => $key,
                    'model_base' => $modelBase,
                    'value' => (float) $items->first()->price,
                    'cost_price' => (float) ($items->first()->cost_price ?? 0),
                ];
            }

            $variants = $this->buildGroupedVariants($items, $colorNames);

            return ['name' => $key, 'model_base' => $modelBase, 'value' => 0, 'variants' => $variants];
        })->values()->toArray();
    }

    /**
     * Agrupa variantes de cor com mesmo preço em "Cor A | Cor B".
     */
    private function buildGroupedVariants(\Illuminate\Support\Collection $items, array $colorNames): array
    {
        $resolved = $items->map(function ($item) use ($colorNames) {
            $colorLabel = $item->color;
            if (!$colorLabel && $item->color_hex) {
                $colorLabel = $colorNames[$item->color_hex] ?? null;
            }

            return [
                'label' => $colorLabel ?: 'Sem cor',
                'color' => $item->color_hex ?: '#cccccc',
                'value' => (float) $item->price,
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
