<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeCustomer;
use App\Domain\Perfumes\Models\PerfumeOrder;
use App\Domain\Perfumes\Models\PerfumePayment;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeReservation;
use App\Domain\Perfumes\Models\PerfumeRetailer;
use App\Domain\Perfumes\Models\PerfumeSale;
use App\Domain\Perfumes\Models\PerfumeSample;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPerfumeDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalProducts = PerfumeProduct::where('active', true)->count();
        $totalRetailers = PerfumeRetailer::where('status', 'active')->count();
        $totalStock = PerfumeProduct::where('active', true)->sum('stock_quantity');
        $samplesOut = PerfumeSample::whereIn('status', ['delivered', 'with_retailer'])->count();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthOrders = PerfumeOrder::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $monthRevenue = PerfumeOrder::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $monthCost = DB::table('perfume_order_items')
            ->join('perfume_orders', 'perfume_orders.id', '=', 'perfume_order_items.perfume_order_id')
            ->whereBetween('perfume_orders.created_at', [$monthStart, $monthEnd])
            ->where('perfume_orders.status', '!=', 'cancelled')
            ->sum(DB::raw('perfume_order_items.cost_price * perfume_order_items.quantity'));

        $monthProfit = $monthRevenue - $monthCost;

        $pendingPayments = PerfumeOrder::whereIn('payment_status', ['pending', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $totalPaid = PerfumePayment::sum('amount');
        $pendingAmount = max(0, $pendingPayments - $totalPaid);

        $recentOrders = PerfumeOrder::with('retailer')
            ->latest()
            ->take(5)
            ->get();

        $oldSamples = PerfumeSample::with(['product', 'retailer'])
            ->whereIn('status', ['delivered', 'with_retailer'])
            ->where('delivered_at', '<', now()->subDays(30))
            ->take(5)
            ->get();

        // Métricas B2C (Varejo)
        $totalCustomers = PerfumeCustomer::count();
        $activeReservations = PerfumeReservation::where('status', 'active')->count();
        
        $monthSales = PerfumeSale::whereBetween('sold_at', [$monthStart, $monthEnd])->count();
        $monthSalesRevenue = PerfumeSale::whereBetween('sold_at', [$monthStart, $monthEnd])->sum('total');
        
        $monthSalesCost = DB::table('perfume_sale_items')
            ->join('perfume_sales', 'perfume_sales.id', '=', 'perfume_sale_items.perfume_sale_id')
            ->whereBetween('perfume_sales.sold_at', [$monthStart, $monthEnd])
            ->whereNull('perfume_sales.deleted_at')
            ->sum(DB::raw('perfume_sale_items.cost_price * perfume_sale_items.quantity'));
        
        $monthSalesProfit = $monthSalesRevenue - $monthSalesCost;
        
        $todaySales = PerfumeSale::whereDate('sold_at', now()->toDateString())->sum('total');

        return view('admin.perfumes.dashboard', compact(
            'totalProducts',
            'totalRetailers',
            'totalStock',
            'samplesOut',
            'monthOrders',
            'monthRevenue',
            'monthCost',
            'monthProfit',
            'pendingAmount',
            'recentOrders',
            'oldSamples',
            'totalCustomers',
            'activeReservations',
            'monthSales',
            'monthSalesRevenue',
            'monthSalesProfit',
            'todaySales',
        ));
    }
}
