<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierExitController extends Controller
{
    public function __construct(
        private ConsignmentStockService $consignmentService
    ) {}

    public function index(Request $request): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $baseQuery = ConsignmentStockItem::bySupplier($supplierId)
            ->sold()
            ->whereBetween('sold_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        $periodCount = (clone $baseQuery)->count();
        $periodTotal = (clone $baseQuery)->sum('supplier_cost');

        $exits = (clone $baseQuery)
            ->orderByDesc('sold_at')
            ->paginate(20)
            ->withQueryString();

        return view('supplier.exits.index', compact('exits', 'from', 'to', 'periodTotal', 'periodCount'));
    }

    public function create(Request $request): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        $search = $request->input('search');
        $selectedId = $request->input('item');

        $available = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->when($search, fn ($q) => $q->search($search))
            ->orderBy('name')
            ->limit(50)
            ->get();

        $selected = $selectedId
            ? ConsignmentStockItem::bySupplier($supplierId)->available()->find($selectedId)
            : null;

        return view('supplier.exits.create', compact('available', 'search', 'selected'));
    }

    public function store(Request $request): RedirectResponse
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        $validated = $request->validate([
            'item_id' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = ConsignmentStockItem::bySupplier($supplierId)
            ->available()
            ->findOrFail($validated['item_id']);

        try {
            DB::beginTransaction();

            $this->consignmentService->registerSupplierPortalExit(
                $item,
                $validated['notes'] ?? null,
                auth('supplier')->user()->supplier->name
            );

            DB::commit();

            return redirect()
                ->route('supplier.exits.index')
                ->with('success', "Saída registrada: {$item->name} — R$ " . number_format((float) $item->supplier_cost, 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
