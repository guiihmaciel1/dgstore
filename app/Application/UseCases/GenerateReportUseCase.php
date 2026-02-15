<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Product\Repositories\ProductRepositoryInterface;
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
    public function dashboardData(): array
    {
        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Vendas de hoje
        $todaySales = $this->saleRepository->getTodaySales();
        $todayTotal = $todaySales->sum('total');
        $todayCount = $todaySales->count();

        // Vendas do mês
        $monthTotal = $this->saleRepository->getTotalByDateRange($monthStart, $monthEnd);
        $monthCount = $this->saleRepository->getCountByDateRange($monthStart, $monthEnd);

        // Estoque baixo
        $lowStock = $this->productRepository->getLowStock();

        // Produtos mais vendidos (últimos 30 dias)
        $topProducts = $this->saleRepository->getTopSellingProducts(5);

        // Dados para gráfico de vendas (últimos 7 dias)
        $salesChart = $this->saleRepository->getSalesByDay(7);
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = (float) ($salesChart->get($date->format('Y-m-d'), 0));
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
        ];
    }
}
