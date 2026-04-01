<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
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
            ->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                $totalProfit = $items->sum(fn ($i) => $i->item_profit);
                $totalRevenue = (float) $items->sum('subtotal');
                $totalQty = $items->sum('quantity');

                return [
                    'product' => $product,
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

        $monthExpensesPaid = (float) FinancialTransaction::expense()
            ->paid()
            ->whereNotNull('paid_at')
            ->whereNotNull('account_id')
            ->whereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->sum('amount');

        $realProfit = $monthProfit - $monthExpensesPaid;

        return [
            'today_profit' => $todayProfit,
            'today_revenue' => $todayRevenue,
            'today_margin' => $todayRevenue > 0 ? ($todayProfit / $todayRevenue) * 100 : 0,
            'month_profit' => $monthProfit,
            'month_revenue' => $monthRevenue,
            'month_margin' => $monthMargin,
            'month_expenses_paid' => $monthExpensesPaid,
            'real_profit' => $realProfit,
            'top_products' => $topProfitProducts,
            'category_ranking' => $categoryRanking,
        ];
    }
}
