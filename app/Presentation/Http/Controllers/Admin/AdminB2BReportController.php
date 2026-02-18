<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BOrderItem;
use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BRetailer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminB2BReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'month');
        $startDate = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            'custom' => now()->parse($request->get('start_date', now()->startOfMonth())),
            default => now()->startOfMonth(),
        };
        $endDate = $period === 'custom'
            ? now()->parse($request->get('end_date', now()))->endOfDay()
            : now()->endOfDay();

        $baseQuery = B2BOrder::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $revenue = (float) (clone $baseQuery)->sum('total');
        $ordersCount = (clone $baseQuery)->count();
        $avgTicket = $ordersCount > 0 ? $revenue / $ordersCount : 0.0;

        $profit = (float) (B2BOrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate]);
        })->selectRaw('SUM((unit_price - cost_price) * quantity) as profit')
            ->value('profit') ?? 0);

        $salesByDay = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topRetailers = B2BRetailer::select('b2b_retailers.id', 'b2b_retailers.store_name', 'b2b_retailers.city', 'b2b_retailers.state')
            ->join('b2b_orders', 'b2b_orders.b2b_retailer_id', '=', 'b2b_retailers.id')
            ->where('b2b_orders.status', '!=', 'cancelled')
            ->whereBetween('b2b_orders.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(b2b_orders.total) as total_revenue, COUNT(b2b_orders.id) as orders_count')
            ->groupBy('b2b_retailers.id', 'b2b_retailers.store_name', 'b2b_retailers.city', 'b2b_retailers.state')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $topProducts = B2BOrderItem::select('b2b_order_items.b2b_product_id')
            ->join('b2b_orders', 'b2b_orders.id', '=', 'b2b_order_items.b2b_order_id')
            ->join('b2b_products', 'b2b_products.id', '=', 'b2b_order_items.b2b_product_id')
            ->where('b2b_orders.status', '!=', 'cancelled')
            ->whereBetween('b2b_orders.created_at', [$startDate, $endDate])
            ->selectRaw('b2b_products.name, b2b_products.storage, b2b_products.color, SUM(b2b_order_items.quantity) as total_qty, SUM(b2b_order_items.subtotal) as total_revenue')
            ->groupBy('b2b_order_items.b2b_product_id', 'b2b_products.name', 'b2b_products.storage', 'b2b_products.color')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $recentOrders = (clone $baseQuery)
            ->with('retailer')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('admin.b2b.reports.index', compact(
            'period',
            'startDate',
            'endDate',
            'revenue',
            'ordersCount',
            'avgTicket',
            'profit',
            'salesByDay',
            'topRetailers',
            'topProducts',
            'recentOrders',
        ));
    }
}
