<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Commission\Models\Commission;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Enums\SaleType;
use App\Domain\Sale\Models\Sale;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ExecutiveSummaryController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'period' => ['required', 'string', 'in:today,week,month,year'],
        ]);

        return response()->json($this->buildAllMetrics($request->period));
    }

    public function buildExecutiveData(string $period): array
    {
        return $this->buildAllMetrics($period);
    }

    private function buildAllMetrics(string $period): array
    {
        [$start, $end, $periodLabel] = $this->resolvePeriod($period);

        $sales = Sale::with(['items.product', 'customer', 'seller', 'commissions'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $allItems = $sales->flatMap->items;

        return [
            'period' => $period,
            'period_label' => $periodLabel,
            'gerencial' => $this->buildGerencial($sales, $allItems),
            'estagiarias' => $this->buildEstagiarias($sales, $start, $end, $period),
            'detalhado' => $this->buildDetalhado($sales),
        ];
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'today' => [
                now()->startOfDay(),
                now()->endOfDay(),
                'Hoje — ' . now()->format('d/m/Y'),
            ],
            'week' => [
                now()->startOfWeek(Carbon::MONDAY),
                now()->endOfWeek(Carbon::SUNDAY),
                'Semana — ' . now()->startOfWeek(Carbon::MONDAY)->format('d/m') . ' a ' . now()->endOfWeek(Carbon::SUNDAY)->format('d/m/Y'),
            ],
            'year' => [
                now()->startOfYear(),
                now()->endOfYear(),
                'Ano — ' . now()->format('Y'),
            ],
            default => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                now()->translatedFormat('F/Y'),
            ],
        };
    }

    // ─── Aba Gerencial ───────────────────────────────────────

    private function buildGerencial(Collection $sales, Collection $allItems): array
    {
        $totalSales = $sales->count();
        $totalRevenue = (float) $sales->sum('total');
        $totalCost = (float) $allItems->sum(fn ($item) => $item->total_cost_value * $item->quantity);
        $totalProfit = (float) $allItems->sum(fn ($item) => $item->item_profit);
        $totalCommissions = (float) $sales->sum(fn ($sale) => $sale->commissions->sum('commission_amount'));
        $netProfit = $totalProfit - $totalCommissions;
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $margin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        $repasseCount = $sales->where('sale_type', SaleType::Repasse)->count();
        $repasseTotal = (float) $sales->where('sale_type', SaleType::Repasse)->sum('total');
        $cfCount = $sales->where('sale_type', SaleType::ClienteFinal)->count();
        $cfTotal = (float) $sales->where('sale_type', SaleType::ClienteFinal)->sum('total');

        $iphoneNew = 0;
        $iphoneNewValue = 0.0;
        $iphoneUsed = 0;
        $iphoneUsedValue = 0.0;
        $accessories = 0;
        $accessoriesValue = 0.0;

        $accessoryCategories = ['charger', 'cable', 'case', 'accessory'];

        foreach ($allItems as $item) {
            $rawCat = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $category = $rawCat instanceof \App\Domain\Product\Enums\ProductCategory ? $rawCat->value : (is_string($rawCat) ? $rawCat : null);

            $rawCond = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
            $condition = $rawCond instanceof ProductCondition ? $rawCond->value : (is_string($rawCond) ? $rawCond : null);

            $qty = $item->quantity;
            $value = (float) $item->subtotal;

            if ($category === 'smartphone') {
                if (in_array($condition, ['used', 'refurbished'])) {
                    $iphoneUsed += $qty;
                    $iphoneUsedValue += $value;
                } else {
                    $iphoneNew += $qty;
                    $iphoneNewValue += $value;
                }
            } elseif (in_array($category, $accessoryCategories)) {
                $accessories += $qty;
                $accessoriesValue += $value;
            }
        }

        $paymentMethods = $sales
            ->groupBy(fn (Sale $s) => $s->payment_method->value)
            ->map(function ($group, $methodValue) {
                $method = PaymentMethod::tryFrom($methodValue);
                return [
                    'label' => $method?->label() ?? ucfirst($methodValue),
                    'count' => $group->count(),
                    'total' => (float) $group->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();

        $topModels = $this->buildTopModels($allItems);
        $topSellers = $this->buildTopSellers($sales);

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'total_commissions' => $totalCommissions,
            'net_profit' => $netProfit,
            'margin' => round($margin, 1),
            'net_margin' => round($netMargin, 1),
            'average_ticket' => round($averageTicket, 2),
            'repasse' => ['count' => $repasseCount, 'total' => $repasseTotal],
            'cliente_final' => ['count' => $cfCount, 'total' => $cfTotal],
            'iphone_new' => ['qty' => $iphoneNew, 'value' => $iphoneNewValue],
            'iphone_used' => ['qty' => $iphoneUsed, 'value' => $iphoneUsedValue],
            'accessories' => ['qty' => $accessories, 'value' => $accessoriesValue],
            'payment_methods' => $paymentMethods,
            'top_models' => $topModels,
            'top_sellers' => $topSellers,
        ];
    }

    private function buildTopModels(Collection $allItems): array
    {
        $smartphones = $allItems->filter(function ($item) {
            $rawCat = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $cat = $rawCat instanceof \App\Domain\Product\Enums\ProductCategory ? $rawCat->value : $rawCat;
            return $cat === 'smartphone';
        });

        return $smartphones
            ->groupBy(fn ($item) => $item->product?->name ?? $item->product_snapshot['name'] ?? 'Desconhecido')
            ->map(fn ($group, $name) => [
                'name' => $name,
                'qty' => $group->sum('quantity'),
                'total' => (float) $group->sum('subtotal'),
            ])
            ->sortByDesc('qty')
            ->take(5)
            ->values()
            ->toArray();
    }

    private function buildTopSellers(Collection $sales): array
    {
        return $sales
            ->groupBy('seller_id')
            ->map(function ($group) {
                $seller = $group->first()->seller;
                return [
                    'name' => $seller?->name ?? $group->first()->seller_name ?? 'Desconhecido',
                    'count' => $group->count(),
                    'total' => (float) $group->sum('total'),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();
    }

    // ─── Aba Estagiárias ─────────────────────────────────────

    private function buildEstagiarias(Collection $allSales, Carbon $start, Carbon $end, string $period): array
    {
        $interns = User::where('role', UserRole::Intern)->where('active', true)->get();

        if ($interns->isEmpty()) {
            return ['interns' => [], 'combined' => [], 'goals' => [], 'daily_chart' => ['labels' => [], 'data' => []]];
        }

        $internIds = $interns->pluck('id');

        $internSales = $allSales->filter(fn (Sale $s) => $internIds->contains($s->seller_id));
        $salesByIntern = $internSales->groupBy('seller_id');

        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = now()->endOfWeek(Carbon::SUNDAY);
        $weekSales = $internSales->filter(fn (Sale $s) => $s->sold_at->between($weekStart, $weekEnd));
        $weekSalesByIntern = $weekSales->groupBy('seller_id');

        $internCommissions = Commission::whereBetween('created_at', [$start, $end])
            ->whereIn('user_id', $internIds)
            ->get()
            ->groupBy('user_id');

        $internsData = $interns->map(function (User $intern) use ($salesByIntern, $weekSalesByIntern, $internCommissions) {
            $sales = $salesByIntern->get($intern->id, collect());
            $weekSalesForIntern = $weekSalesByIntern->get($intern->id, collect());
            $commissions = $internCommissions->get($intern->id, collect());
            $salesCount = $sales->count();
            $totalRevenue = (float) $sales->sum('total');

            return [
                'name' => $intern->name,
                'sales_count' => $salesCount,
                'total_revenue' => $totalRevenue,
                'commission_earned' => (float) $commissions->sum('commission_amount'),
                'sales_this_week' => $weekSalesForIntern->count(),
                'avg_ticket' => $salesCount > 0 ? round($totalRevenue / $salesCount, 2) : 0,
            ];
        })->sortByDesc('sales_count')->values()->toArray();

        $combinedSales = array_sum(array_column($internsData, 'sales_count'));
        $combinedRevenue = array_sum(array_column($internsData, 'total_revenue'));
        $combinedCommission = array_sum(array_column($internsData, 'commission_earned'));
        $combinedWeekSales = array_sum(array_column($internsData, 'sales_this_week'));

        $monthlyGoals = collect([
            ['target' => 30, 'reward' => 'Colônia Victoria Secrets para cada'],
            ['target' => 40, 'reward' => 'Kit Colônia + Creme Victoria Secrets para cada'],
            ['target' => 50, 'reward' => 'Perfume Árabe para cada'],
        ])->map(fn ($goal) => [
            ...$goal,
            'reached' => $combinedSales >= $goal['target'],
            'progress' => min(100, $goal['target'] > 0 ? round(($combinedSales / $goal['target']) * 100) : 0),
        ])->toArray();

        $weeklyGoal = [
            'target' => 10,
            'current' => $combinedWeekSales,
            'reached' => $combinedWeekSales >= 10,
            'progress' => min(100, round(($combinedWeekSales / 10) * 100)),
            'remaining' => max(0, 10 - $combinedWeekSales),
        ];

        $dailyChart = $this->buildDailyChart($internSales, $start, $end);

        return [
            'interns' => $internsData,
            'combined' => [
                'total_sales' => $combinedSales,
                'total_revenue' => $combinedRevenue,
                'total_commission' => $combinedCommission,
                'sales_this_week' => $combinedWeekSales,
            ],
            'goals' => ['monthly' => $monthlyGoals, 'weekly' => $weeklyGoal],
            'daily_chart' => $dailyChart,
        ];
    }

    private function buildDailyChart(Collection $sales, Carbon $start, Carbon $end): array
    {
        $lastDay = $end->isFuture() ? now()->day : $end->day;
        $dailyData = [];
        for ($d = 1; $d <= $lastDay; $d++) {
            $dailyData[$d] = 0;
        }
        foreach ($sales as $sale) {
            $day = $sale->sold_at->day;
            if (isset($dailyData[$day])) {
                $dailyData[$day]++;
            }
        }

        return [
            'labels' => array_map(fn ($d) => str_pad((string) $d, 2, '0', STR_PAD_LEFT), array_keys($dailyData)),
            'data' => array_values($dailyData),
        ];
    }

    // ─── Aba Detalhado ───────────────────────────────────────

    private function buildDetalhado(Collection $sales): array
    {
        return $sales->map(function (Sale $sale) {
            $items = $sale->items;
            $firstItem = $items->first();
            $snapshot = $firstItem?->product_snapshot ?? [];

            $productName = $items->count() > 1
                ? $items->count() . ' itens'
                : ($firstItem?->product?->name ?? $snapshot['name'] ?? '—');

            $rawCondition = $firstItem?->product?->condition ?? ($snapshot['condition'] ?? null);
            $condition = $rawCondition instanceof ProductCondition ? $rawCondition->value : (is_string($rawCondition) ? $rawCondition : null);
            $conditionLabel = match ($condition) {
                'new' => 'Novo',
                'used' => 'Usado',
                'refurbished' => 'Recond.',
                default => '—',
            };

            $totalCost = (float) $items->sum(fn ($i) => $i->total_cost_value * $i->quantity);
            $totalProfit = (float) $items->sum(fn ($i) => $i->item_profit);

            return [
                'date' => $sale->sold_at->format('d/m/Y'),
                'date_raw' => $sale->sold_at->toDateTimeString(),
                'seller' => $sale->seller?->name ?? $sale->seller_name ?? '—',
                'customer' => $sale->customer?->name ?? 'Sem cliente',
                'type' => $sale->sale_type === SaleType::Repasse ? 'Repasse' : 'Cliente Final',
                'type_raw' => $sale->sale_type?->value ?? 'cliente_final',
                'product' => $productName,
                'condition' => $conditionLabel,
                'value' => (float) $sale->total,
                'cost' => $totalCost,
                'profit' => $totalProfit,
                'payment' => $sale->payment_method->label(),
            ];
        })->sortByDesc('date_raw')->values()->toArray();
    }
}
