<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BOrderItem;
use App\Domain\B2B\Models\B2BRetailer;
use App\Domain\B2B\Models\B2BSetting;
use App\Domain\B2B\Models\B2BProduct;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminB2BDashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = now();

        $monthRevenue = (float) B2BOrder::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $monthProfit = (float) (B2BOrderItem::whereHas('order', function ($q) use ($now) {
            $q->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->where('status', '!=', 'cancelled');
        })->selectRaw('SUM((unit_price - cost_price) * quantity) as profit')
            ->value('profit') ?? 0);

        $monthOrders = B2BOrder::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('status', '!=', 'cancelled')
            ->count();

        $pendingRetailers = B2BRetailer::where('status', 'pending')->latest()->get();

        $lowStockThreshold = B2BSetting::getLowStockThreshold();
        $lowStockProducts = B2BProduct::where('active', true)
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $recentOrders = B2BOrder::with('retailer')
            ->latest()
            ->limit(5)
            ->get();

        $topRetailers = B2BRetailer::select('b2b_retailers.*')
            ->selectRaw('(SELECT COALESCE(SUM(total), 0) FROM b2b_orders WHERE b2b_orders.b2b_retailer_id = b2b_retailers.id AND b2b_orders.status != ?) as total_purchased', ['cancelled'])
            ->selectRaw('(SELECT COUNT(*) FROM b2b_orders WHERE b2b_orders.b2b_retailer_id = b2b_retailers.id AND b2b_orders.status != ?) as orders_count', ['cancelled'])
            ->having('total_purchased', '>', 0)
            ->orderByDesc('total_purchased')
            ->limit(5)
            ->get();

        $activeOrders = B2BOrder::whereNotIn('status', ['completed', 'cancelled'])->count();

        return view('admin.b2b.dashboard', compact(
            'monthRevenue',
            'monthProfit',
            'monthOrders',
            'pendingRetailers',
            'lowStockProducts',
            'lowStockThreshold',
            'recentOrders',
            'topRetailers',
            'activeOrders',
        ));
    }
}
