<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Models\ConsignmentStockMovement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierDashboardController extends Controller
{
    public function index(): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        
        $availableItems = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->get();
        
        $soldItems = ConsignmentStockItem::bySupplier($supplierId)
            ->sold()
            ->get();
        
        $stats = [
            'available_count' => $availableItems->sum('quantity'),
            'available_value' => $availableItems->sum(fn($item) => $item->quantity * $item->supplier_cost),
            'sold_count' => $soldItems->sum('quantity'),
            'sold_value' => ConsignmentStockMovement::where('type', 'out')
                ->whereHas('item', fn($q) => $q->where('supplier_id', $supplierId))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get()
                ->sum(fn($mov) => $mov->quantity * $mov->item->supplier_cost),
        ];
        
        $recentItems = ConsignmentStockItem::bySupplier($supplierId)
            ->with('batch')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('supplier.dashboard', compact('stats', 'recentItems'));
    }
}
