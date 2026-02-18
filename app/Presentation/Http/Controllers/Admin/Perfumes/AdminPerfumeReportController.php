<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeOrder;
use App\Domain\Perfumes\Models\PerfumeOrderItem;
use App\Domain\Perfumes\Models\PerfumeRetailer;
use App\Domain\Perfumes\Models\PerfumeSample;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPerfumeReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $revenue = PerfumeOrder::whereBetween('created_at', [$from, "{$to} 23:59:59"])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $cost = DB::table('perfume_order_items')
            ->join('perfume_orders', 'perfume_orders.id', '=', 'perfume_order_items.perfume_order_id')
            ->whereBetween('perfume_orders.created_at', [$from, "{$to} 23:59:59"])
            ->where('perfume_orders.status', '!=', 'cancelled')
            ->sum(DB::raw('perfume_order_items.cost_price * perfume_order_items.quantity'));

        $profit = $revenue - $cost;

        $topRetailers = PerfumeRetailer::select('perfume_retailers.*')
            ->join('perfume_orders', 'perfume_orders.perfume_retailer_id', '=', 'perfume_retailers.id')
            ->whereBetween('perfume_orders.created_at', [$from, "{$to} 23:59:59"])
            ->where('perfume_orders.status', '!=', 'cancelled')
            ->groupBy('perfume_retailers.id')
            ->selectRaw('SUM(perfume_orders.total) as total_spent')
            ->selectRaw('COUNT(perfume_orders.id) as orders_count')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        $topProducts = DB::table('perfume_order_items')
            ->join('perfume_orders', 'perfume_orders.id', '=', 'perfume_order_items.perfume_order_id')
            ->join('perfume_products', 'perfume_products.id', '=', 'perfume_order_items.perfume_product_id')
            ->whereBetween('perfume_orders.created_at', [$from, "{$to} 23:59:59"])
            ->where('perfume_orders.status', '!=', 'cancelled')
            ->groupBy('perfume_products.id', 'perfume_products.name', 'perfume_products.brand')
            ->select(
                'perfume_products.name',
                'perfume_products.brand',
                DB::raw('SUM(perfume_order_items.quantity) as total_qty'),
                DB::raw('SUM(perfume_order_items.subtotal) as total_revenue'),
            )
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        $samplesInField = PerfumeSample::with(['product', 'retailer'])
            ->whereIn('status', ['delivered', 'with_retailer'])
            ->latest('delivered_at')
            ->get();

        $overdue = PerfumeOrder::with('retailer')
            ->where('payment_method', 'consignment')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        return view('admin.perfumes.reports.index', compact(
            'from', 'to', 'revenue', 'cost', 'profit',
            'topRetailers', 'topProducts', 'samplesInField', 'overdue',
        ));
    }
}
