<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Commission\Models\Commission;
use App\Domain\Commission\Models\CommissionWithdrawal;
use App\Domain\Customer\Models\Customer;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\User\Models\User;
use Carbon\Carbon;

class GenerateReportUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Gera relatório de vendas por período
     */
    public function salesReport(Carbon $startDate, Carbon $endDate): array
    {
        $sales = $this->saleRepository->getByDateRange($startDate, $endDate);

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $totalDiscount = $sales->sum('discount');
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Vendas por método de pagamento
        $byPaymentMethod = $sales->groupBy(fn($sale) => $sale->payment_method->value)
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ]);

        // Vendas por status
        $byStatus = $sales->groupBy(fn($sale) => $sale->payment_status->value)
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ]);

        // Vendas por vendedor
        $bySeller = $sales->groupBy('user_id')
            ->map(function ($group) {
                $user = $group->first()->user;
                return [
                    'seller_name' => $user?->name ?? 'N/A',
                    'count' => $group->count(),
                    'total' => $group->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'summary' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'average_ticket' => $averageTicket,
            ],
            'by_payment_method' => $byPaymentMethod,
            'by_status' => $byStatus,
            'by_seller' => $bySeller,
            'sales' => $sales,
        ];
    }

    /**
     * Gera relatório de produtos mais vendidos
     */
    public function topProductsReport(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $topProducts = $this->saleRepository->getTopSellingProducts($limit, $startDate, $endDate);

        return [
            'period' => $startDate && $endDate ? [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ] : null,
            'products' => $topProducts,
        ];
    }

    /**
     * Gera relatório de estoque
     */
    public function stockReport(): array
    {
        $lowStock = $this->productRepository->getLowStock();
        $allProducts = $this->productRepository->getActive();

        $totalProducts = $allProducts->count();
        $totalUnits = $allProducts->sum('stock_quantity');
        $outOfStock = $allProducts->filter(fn($p) => $p->stock_quantity <= 0)->count();
        $lowStockCount = $lowStock->count();

        // Por categoria
        $byCategory = $allProducts->groupBy(fn($p) => $p->category->value)
            ->map(fn($group) => [
                'count' => $group->count(),
                'stock_quantity' => $group->sum('stock_quantity'),
            ]);

        return [
            'summary' => [
                'total_products' => $totalProducts,
                'total_units' => $totalUnits,
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStockCount,
            ],
            'by_category' => $byCategory,
            'low_stock_products' => $lowStock,
        ];
    }

    /**
     * Gera dados para o dashboard
     */
    public function dashboardData(?Carbon $referenceDate = null): array
    {
        $ref = $referenceDate ?? Carbon::now();
        $monthStart = $ref->copy()->startOfMonth();
        $monthEnd = $ref->copy()->endOfMonth();
        $isCurrentMonth = $ref->isSameMonth(Carbon::now());

        $todaySales = $this->saleRepository->getTodaySales();
        $todayTotal = $todaySales->sum('total');
        $todayCount = $todaySales->count();

        $monthTotal = $this->saleRepository->getTotalByDateRange($monthStart, $monthEnd);
        $monthCount = $this->saleRepository->getCountByDateRange($monthStart, $monthEnd);

        $lowStock = $this->productRepository->getLowStock();

        $topProducts = $this->saleRepository->getTopSellingProducts(5, $monthStart, $monthEnd);

        $salesChart = $this->saleRepository->getSalesByDay($isCurrentMonth ? 7 : $monthEnd->day, $isCurrentMonth ? null : $monthStart);
        $chartLabels = [];
        $chartData = [];

        if ($isCurrentMonth) {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartLabels[] = $date->format('d/m');
                $chartData[] = (float) ($salesChart->get($date->format('Y-m-d'), 0));
            }
        } else {
            for ($day = 1; $day <= $monthEnd->day; $day++) {
                $date = $monthStart->copy()->day($day);
                $chartLabels[] = $date->format('d/m');
                $chartData[] = (float) ($salesChart->get($date->format('Y-m-d'), 0));
            }
        }

        $prevMonthStart = $monthStart->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $monthStart->copy()->subMonth()->endOfMonth();

        return [
            'today' => [
                'total' => $todayTotal,
                'count' => $todayCount,
            ],
            'month' => [
                'total' => $monthTotal,
                'count' => $monthCount,
            ],
            'low_stock' => [
                'count' => $lowStock->count(),
                'products' => $lowStock->take(5),
            ],
            'top_products' => $topProducts,
            'sales_chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
            ],
            'profit' => $this->profitData($monthStart, $monthEnd),
            'prev_month_profit' => $this->prevMonthProfitSummary($prevMonthStart, $prevMonthEnd),
        ];
    }

    /**
     * Calcula dados de lucro/margem para o dashboard.
     * Usa uma única query com eager loading para evitar N+1.
     */
    private function profitData(Carbon $monthStart, Carbon $monthEnd): array
    {
        $monthSales = Sale::with(['items.product'])
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $today = Carbon::today();
        $todaySales = $monthSales->filter(fn (Sale $s) => $s->sold_at->isToday());

        $todayProfit = $todaySales->sum(fn (Sale $s) => $s->profit);
        $todayRevenue = (float) $todaySales->sum('total');

        $monthProfit = $monthSales->sum(fn (Sale $s) => $s->profit);
        $monthRevenue = (float) $monthSales->sum('total');
        $monthMargin = $monthRevenue > 0 ? ($monthProfit / $monthRevenue) * 100 : 0;

        $allItems = $monthSales->flatMap->items;

        $topProfitProducts = $allItems
            ->groupBy(function ($item) {
                if ($item->product_id) {
                    return 'pid_' . $item->product_id;
                }
                $snapshot = $item->product_snapshot ?? [];
                return 'snap_' . ($snapshot['name'] ?? 'unknown');
            })
            ->map(function ($items) {
                $first = $items->first();
                $product = $first->product;
                $snapshot = $first->product_snapshot ?? [];
                $name = $product?->name ?? $snapshot['name'] ?? 'Produto removido';
                $sku = $product?->sku ?? $snapshot['sku'] ?? null;

                $totalProfit = $items->sum(fn ($i) => $i->item_profit);
                $totalRevenue = (float) $items->sum('subtotal');
                $totalQty = $items->sum('quantity');

                return [
                    'product' => $product,
                    'name' => $name,
                    'sku' => $sku,
                    'profit' => $totalProfit,
                    'revenue' => $totalRevenue,
                    'margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
                    'quantity' => $totalQty,
                ];
            })
            ->sortByDesc('profit')
            ->take(5)
            ->values();

        $categoryRanking = $allItems
            ->groupBy(function ($item) {
                $snapshot = $item->product_snapshot ?? [];
                return $item->product?->category?->value ?? $snapshot['category'] ?? 'outros';
            })
            ->map(function ($items, $categoryValue) {
                $category = ProductCategory::tryFrom($categoryValue);
                $totalProfit = $items->sum(fn ($i) => $i->item_profit);
                $totalRevenue = (float) $items->sum('subtotal');
                $totalQty = $items->sum('quantity');

                return [
                    'category' => $categoryValue,
                    'label' => $category?->label() ?? ucfirst($categoryValue),
                    'profit' => $totalProfit,
                    'revenue' => $totalRevenue,
                    'margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
                    'quantity' => $totalQty,
                ];
            })
            ->sortByDesc('profit')
            ->values();

        $salaryCategory = \App\Domain\Finance\Models\FinancialCategory::where('name', 'like', '%Salário%')
            ->orWhere('name', 'like', '%Salario%')
            ->orWhere('name', 'like', '%salário%')
            ->orWhere('name', 'like', '%salarios%')
            ->pluck('id');

        $expensesQuery = FinancialTransaction::expense()
            ->paid()
            ->whereNotNull('paid_at')
            ->whereNotNull('account_id')
            ->whereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()]);

        $monthExpensesPaid = (float) (clone $expensesQuery)->sum('amount');

        $salaryTransactions = $salaryCategory->isNotEmpty()
            ? (clone $expensesQuery)->whereIn('category_id', $salaryCategory)->orderBy('amount', 'desc')->get()
            : collect();

        $salariesPaid = (float) $salaryTransactions->sum('amount');
        $salaryDetails = $salaryTransactions->map(fn ($t) => [
            'description' => $t->description,
            'amount' => (float) $t->amount,
            'paid_at' => $t->paid_at?->format('d/m'),
        ])->values()->toArray();

        $expensesWithoutSalaries = $monthExpensesPaid - $salariesPaid;
        $realProfit = $monthProfit - $monthExpensesPaid;

        return [
            'today_profit' => $todayProfit,
            'today_revenue' => $todayRevenue,
            'today_margin' => $todayRevenue > 0 ? ($todayProfit / $todayRevenue) * 100 : 0,
            'month_profit' => $monthProfit,
            'month_revenue' => $monthRevenue,
            'month_margin' => $monthMargin,
            'month_expenses_paid' => $monthExpensesPaid,
            'salaries_paid' => $salariesPaid,
            'salary_details' => $salaryDetails,
            'expenses_without_salaries' => $expensesWithoutSalaries,
            'real_profit' => $realProfit,
            'top_products' => $topProfitProducts,
            'category_ranking' => $categoryRanking,
        ];
    }

    private function prevMonthProfitSummary(Carbon $monthStart, Carbon $monthEnd): array
    {
        $monthSales = Sale::with(['items.product'])
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $monthProfit = $monthSales->sum(fn (Sale $s) => $s->profit);
        $monthRevenue = (float) $monthSales->sum('total');

        $expensesPaid = (float) FinancialTransaction::expense()
            ->paid()
            ->whereNotNull('paid_at')
            ->whereNotNull('account_id')
            ->whereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->sum('amount');

        $realProfit = $monthProfit - $expensesPaid;

        return [
            'month_label' => $monthStart->translatedFormat('M/Y'),
            'month_profit' => $monthProfit,
            'month_revenue' => $monthRevenue,
            'month_expenses' => $expensesPaid,
            'real_profit' => $realProfit,
        ];
    }

    // ─── Comparativo de Vendas ───

    public function salesComparison(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): array
    {
        $period1 = $this->salesReport($start1, $end1);
        $period2 = $this->salesReport($start2, $end2);
        $deltas = $this->calculateDeltas($period1['summary'], $period2['summary']);

        return [
            'period1' => $period1,
            'period2' => $period2,
            'deltas' => $deltas,
        ];
    }

    private function calculateDeltas(array $current, array $previous): array
    {
        $deltas = [];
        foreach ($current as $key => $value) {
            $prev = (float) ($previous[$key] ?? 0);
            $curr = (float) $value;

            if ($prev == 0) {
                $deltas[$key] = $curr > 0 ? 100.0 : 0.0;
            } else {
                $deltas[$key] = (($curr - $prev) / abs($prev)) * 100;
            }
        }

        return $deltas;
    }

    // ─── Relatório de Margens ───

    public function marginsReport(Carbon $startDate, Carbon $endDate): array
    {
        $items = SaleItem::with(['product', 'sale'])
            ->whereHas('sale', fn ($q) => $q
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->where('payment_status', '!=', PaymentStatus::Cancelled)
            )
            ->get();

        $byCategory = $this->groupMarginData($items, function (SaleItem $item) {
            $raw = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $value = $raw instanceof ProductCategory ? $raw->value : (is_string($raw) ? $raw : 'outros');
            $category = ProductCategory::tryFrom($value);

            return [
                'key' => $value,
                'label' => $category?->label() ?? ucfirst($value),
            ];
        });

        $bySupplier = $this->groupMarginData($items, function (SaleItem $item) {
            $supplier = $item->product?->supplier ?? $item->product_snapshot['supplier'] ?? null;
            $name = $supplier && trim($supplier) !== '' ? trim($supplier) : 'Não informado';

            return [
                'key' => mb_strtolower($name),
                'supplier' => $name,
            ];
        });

        $byCondition = $this->groupMarginData($items, function (SaleItem $item) {
            $raw = $item->product?->condition ?? ($item->product_snapshot['condition'] ?? null);
            $value = $raw instanceof ProductCondition ? $raw->value : (is_string($raw) ? $raw : 'unknown');

            $labels = [
                'new' => 'Novo',
                'used' => 'Seminovo',
                'refurbished' => 'Recondicionado',
            ];

            return [
                'key' => $value,
                'label' => $labels[$value] ?? ucfirst($value),
            ];
        });

        $totalRevenue = $items->sum('subtotal');
        $totalCost = $items->sum(fn ($i) => $i->total_cost_value * $i->quantity);
        $totalProfit = $items->sum(fn ($i) => $i->item_profit);

        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'summary' => [
                'total_items' => $items->count(),
                'total_quantity' => $items->sum('quantity'),
                'total_revenue' => (float) $totalRevenue,
                'total_cost' => (float) $totalCost,
                'total_profit' => (float) $totalProfit,
                'avg_margin' => $totalRevenue > 0 ? ($totalProfit / (float) $totalRevenue) * 100 : 0,
            ],
            'by_category' => $byCategory->sortByDesc('profit')->values()->toArray(),
            'by_supplier' => $bySupplier->sortByDesc('profit')->values()->toArray(),
            'by_condition' => $byCondition->sortByDesc('profit')->values()->toArray(),
        ];
    }

    private function groupMarginData($items, callable $keyFn): \Illuminate\Support\Collection
    {
        return $items->groupBy(fn ($item) => $keyFn($item)['key'])
            ->map(function ($group) use ($keyFn) {
                $first = $group->first();
                $meta = $keyFn($first);
                $revenue = (float) $group->sum('subtotal');
                $cost = (float) $group->sum(fn ($i) => $i->total_cost_value * $i->quantity);
                $profit = (float) $group->sum(fn ($i) => $i->item_profit);
                $qty = $group->sum('quantity');

                return array_merge($meta, [
                    'quantity' => $qty,
                    'revenue' => $revenue,
                    'cost' => $cost,
                    'profit' => $profit,
                    'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                ]);
            });
    }

    // ─── Relatório de Comissões ───

    public function commissionsReport(Carbon $startDate, Carbon $endDate, ?string $userId = null): array
    {
        $commissionsQuery = Commission::with(['user', 'sale.items.product'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($userId) {
            $commissionsQuery->where('user_id', $userId);
        }

        $commissions = $commissionsQuery->get();

        $withdrawalsQuery = CommissionWithdrawal::with('user')
            ->approved()
            ->whereBetween('date', [$startDate, $endDate]);

        if ($userId) {
            $withdrawalsQuery->where('user_id', $userId);
        }

        $withdrawals = $withdrawalsQuery->get();

        $bySeller = $commissions->groupBy('user_id')
            ->map(function ($userCommissions) use ($withdrawals) {
                $user = $userCommissions->first()->user;
                $uid = $user?->id;
                $approved = $userCommissions->where('status', 'approved');
                $salesCommissions = $approved->where('is_manual', false);
                $manualCommissions = $approved->where('is_manual', true);

                $userWithdrawals = $uid ? $withdrawals->where('user_id', $uid) : collect();

                $totalCommission = (float) $approved->sum('commission_amount');
                $totalWithdrawn = (float) $userWithdrawals->sum('amount');

                return [
                    'user_id' => $uid,
                    'name' => $user?->name ?? 'N/A',
                    'sales_count' => $salesCommissions->count(),
                    'sales_total' => (float) $salesCommissions->sum('sale_total'),
                    'commission_total' => $totalCommission,
                    'manual_total' => (float) $manualCommissions->sum('commission_amount'),
                    'withdrawn' => $totalWithdrawn,
                    'balance' => $totalCommission - $totalWithdrawn,
                ];
            })
            ->sortByDesc('commission_total')
            ->values()
            ->toArray();

        $byCategory = $this->commissionsByCategory($commissions);

        $totalCommissions = (float) $commissions->where('status', 'approved')->sum('commission_amount');
        $totalWithdrawn = (float) $withdrawals->sum('amount');

        $sellers = User::where('active', true)
            ->whereIn('role', ['admin_geral', 'seller', 'intern'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'summary' => [
                'total_commissions' => $totalCommissions,
                'total_withdrawn' => $totalWithdrawn,
                'total_balance' => $totalCommissions - $totalWithdrawn,
                'total_sellers' => count($bySeller),
            ],
            'by_seller' => $bySeller,
            'by_category' => $byCategory,
            'sellers' => $sellers,
        ];
    }

    private function commissionsByCategory($commissions): array
    {
        $approved = $commissions->where('status', 'approved')->where('is_manual', false);
        $allItems = collect();

        foreach ($approved as $commission) {
            if (!$commission->sale) {
                continue;
            }

            foreach ($commission->sale->items as $item) {
                $allItems->push([
                    'item' => $item,
                    'commission_share' => $commission->sale->items->count() > 0
                        ? (float) $commission->commission_amount / $commission->sale->items->count()
                        : 0,
                ]);
            }
        }

        return $allItems->groupBy(function ($entry) {
            $item = $entry['item'];
            $raw = $item->product?->category ?? ($item->product_snapshot['category'] ?? null);
            $value = $raw instanceof ProductCategory ? $raw->value : (is_string($raw) ? $raw : 'outros');

            return $value;
        })->map(function ($group, $categoryValue) {
            $category = ProductCategory::tryFrom($categoryValue);

            return [
                'category' => $categoryValue,
                'label' => $category?->label() ?? ucfirst($categoryValue),
                'quantity' => $group->sum(fn ($e) => $e['item']->quantity),
                'revenue' => (float) $group->sum(fn ($e) => (float) $e['item']->subtotal),
                'commission' => (float) $group->sum(fn ($e) => $e['commission_share']),
            ];
        })->sortByDesc('commission')->values()->toArray();
    }

    // ─── Dashboard Executivo ───

    public function executiveReport(Carbon $referenceDate): array
    {
        $current = $this->periodMetrics($referenceDate);
        $previousMonth = $this->periodMetrics($referenceDate->copy()->subMonth());
        $sameMonthLastYear = $this->periodMetrics($referenceDate->copy()->subYear());
        $monthlyEvolution = $this->monthlyEvolution($referenceDate, 12);
        $topSellers = $this->topSellers($referenceDate);

        return [
            'reference_date' => $referenceDate,
            'current' => $current,
            'previous_month' => $previousMonth,
            'same_month_last_year' => $sameMonthLastYear,
            'deltas_prev' => $this->calculateDeltas($current, $previousMonth),
            'deltas_year' => $this->calculateDeltas($current, $sameMonthLastYear),
            'monthly_evolution' => $monthlyEvolution,
            'top_sellers' => $topSellers,
        ];
    }

    private function periodMetrics(Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth();
        $end = $ref->copy()->endOfMonth();

        $sales = Sale::with('items.product')
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $revenue = (float) $sales->sum('total');
        $count = $sales->count();
        $profit = $sales->sum(fn (Sale $s) => $s->profit);
        $ticket = $count > 0 ? $revenue / $count : 0;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        $newCustomers = Customer::whereBetween('created_at', [$start, $end])->count();

        return [
            'revenue' => $revenue,
            'profit' => $profit,
            'count' => $count,
            'ticket' => $ticket,
            'margin' => $margin,
            'new_customers' => $newCustomers,
        ];
    }

    private function monthlyEvolution(Carbon $ref, int $months): array
    {
        $result = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $ref->copy()->subMonths($i);
            $metrics = $this->periodMetrics($date);
            $metrics['label'] = $date->translatedFormat('M/Y');
            $metrics['month'] = $date->format('Y-m');
            $result[] = $metrics;
        }

        return $result;
    }

    private function topSellers(Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth();
        $end = $ref->copy()->endOfMonth();

        $sales = Sale::with(['user', 'items.product'])
            ->whereBetween('sold_at', [$start, $end])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        return $sales->groupBy('user_id')
            ->map(function ($group) {
                $user = $group->first()->user;
                $revenue = (float) $group->sum('total');
                $count = $group->count();
                $profit = $group->sum(fn (Sale $s) => $s->profit);

                return [
                    'name' => $user?->name ?? 'N/A',
                    'count' => $count,
                    'revenue' => $revenue,
                    'profit' => $profit,
                    'ticket' => $count > 0 ? $revenue / $count : 0,
                    'margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values()
            ->toArray();
    }
}
