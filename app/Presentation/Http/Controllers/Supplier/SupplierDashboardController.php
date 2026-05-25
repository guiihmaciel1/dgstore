<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SupplierDashboardController extends Controller
{
    public function index(): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        $availableItems = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->get();

        $stats = [
            'available_count' => $availableItems->sum('quantity'),
        ];

        $recentItems = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->with('batch')
            ->latest()
            ->limit(10)
            ->get();

        return view('supplier.dashboard', compact('stats', 'recentItems'));
    }
}
