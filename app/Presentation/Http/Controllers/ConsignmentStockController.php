<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use Illuminate\Support\Facades\DB;
use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ConsignmentStockController extends Controller
{
    public function __construct(
        private readonly ConsignmentStockService $service,
    ) {}

    public function index(Request $request): View
    {
        $query = ConsignmentStockItem::with('supplier', 'batch');

        if ($request->filled('supplier_id')) {
            $query->bySupplier($request->supplier_id);
        }

        $statusFilter = $request->get('status', 'available');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $items = $query->orderByDesc('received_at')->paginate(30)->withQueryString();

        $suppliers = Supplier::active()->orderBy('name')->get();

        $stats = [
            'available' => ConsignmentStockItem::available()->sum('available_quantity'),
            'available_value' => ConsignmentStockItem::available()->sum(DB::raw('available_quantity * supplier_cost')),
            'sold' => ConsignmentStockItem::sold()->count(),
            'sold_value' => ConsignmentStockItem::sold()->sum(DB::raw('quantity * supplier_cost')),
        ];

        return view('stock.consignment.index', [
            'items' => $items,
            'suppliers' => $suppliers,
            'stats' => $stats,
            'filters' => array_merge($request->only(['supplier_id', 'search']), ['status' => $statusFilter]),
        ]);
    }

    public function create(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('stock.consignment.create', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request): RedirectResponse|View
    {
        $validated = $this->validateEntryData($request);

        $divergentItems = $this->service->detectPriceDivergence($validated);

        if ($divergentItems->isNotEmpty()) {
            $suppliers = Supplier::active()->orderBy('name')->get();
            $selectedSupplier = Supplier::find($validated['supplier_id']);

            return view('stock.consignment.confirm-entry', [
                'formData' => $validated,
                'divergentItems' => $divergentItems,
                'suppliers' => $suppliers,
                'selectedSupplier' => $selectedSupplier,
            ]);
        }

        $this->service->registerEntry($validated, auth()->id());

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', 'Entrada de estoque consignado registrada com sucesso!');
    }

    public function storeConfirmed(Request $request): RedirectResponse
    {
        $validated = $this->validateEntryData($request);

        $updatePrices = $request->boolean('update_prices');
        $reason = $request->input('price_update_reason', '');

        $item = $this->service->registerEntry($validated, auth()->id());

        if ($updatePrices && $item->batch) {
            $divergentItems = $this->service->detectPriceDivergence(array_merge($validated, [
                'supplier_cost' => $validated['supplier_cost'],
            ]));

            $itemsToUpdate = $divergentItems->where('id', '!=', $item->id);

            if ($itemsToUpdate->isNotEmpty()) {
                $this->service->updatePricesFromBatch(
                    $item->batch,
                    $itemsToUpdate,
                    $reason ?: 'Atualização de preço por novo lote ' . $item->batch->batch_code,
                    auth()->id(),
                );
            }
        }

        $message = 'Entrada de estoque consignado registrada com sucesso!';
        if ($updatePrices) {
            $message .= ' Preços anteriores foram atualizados.';
        }

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', $message);
    }

    public function edit(ConsignmentStockItem $item): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $priceHistory = $this->service->getPriceHistoryForItem($item->id);

        return view('stock.consignment.edit', [
            'item' => $item->load('batch'),
            'suppliers' => $suppliers,
            'priceHistory' => $priceHistory,
        ]);
    }

    public function update(Request $request, ConsignmentStockItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'has_box' => ['nullable'],
            'has_cable' => ['nullable'],
            'imei' => ['nullable', 'string', 'max:50', 'unique:consignment_stock_items,imei,' . $item->id],
            'supplier_cost' => ['required', 'numeric', 'min:0'],
            'suggested_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'received_at' => ['nullable', 'date'],
        ]);

        $validated['has_box'] = $request->boolean('has_box');
        $validated['has_cable'] = $request->boolean('has_cable');

        if ($validated['condition'] === 'new') {
            $validated['battery_health'] = null;
            $validated['has_box'] = false;
            $validated['has_cable'] = false;
        }

        $oldQuantity = $item->quantity;
        $newQuantity = (int) $validated['quantity'];
        $quantityDiff = $newQuantity - $oldQuantity;

        $item->update(array_merge($validated, [
            'available_quantity' => max(0, $item->available_quantity + $quantityDiff),
        ]));

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', 'Item consignado atualizado com sucesso!');
    }

    public function returnItem(Request $request, ConsignmentStockItem $item): RedirectResponse
    {
        if (!$item->isAvailable()) {
            return redirect()->back()->with('error', 'Este item não está disponível para devolução.');
        }

        $this->service->registerReturn($item, auth()->id(), $request->input('reason'));

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', 'Item devolvido ao fornecedor com sucesso!');
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->get('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $items = $this->service->searchAvailable($term);

        return response()->json(
            $items->map(fn (ConsignmentStockItem $item) => [
                'id' => $item->id,
                'name' => $item->full_name,
                'imei' => $item->imei,
                'supplier_cost' => (float) $item->supplier_cost,
                'suggested_price' => $item->suggested_price ? (float) $item->suggested_price : null,
                'supplier_name' => $item->supplier->name,
                'available_quantity' => $item->available_quantity,
                'batch_code' => $item->batch?->batch_code,
                'is_consignment' => true,
                'consignment_item_id' => $item->id,
            ])
        );
    }

    public function report(Request $request): View
    {
        $supplierId = $request->get('supplier_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $suppliers = Supplier::active()->orderBy('name')->get();

        $available = $supplierId
            ? $this->service->getAvailableBySupplier($supplierId)
            : collect();

        $sold = $supplierId
            ? $this->service->getSoldBySupplier($supplierId, $dateFrom, $dateTo)
            : collect();

        $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;

        return view('stock.consignment.report', [
            'suppliers' => $suppliers,
            'available' => $available,
            'sold' => $sold,
            'selectedSupplier' => $selectedSupplier,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    private function validateEntryData(Request $request): array
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'has_box' => ['nullable'],
            'has_cable' => ['nullable'],
            'imei' => ['nullable', 'string', 'max:50', 'unique:consignment_stock_items,imei'],
            'supplier_cost' => ['required', 'numeric', 'min:0'],
            'suggested_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'received_at' => ['nullable', 'date'],
        ]);

        $validated['has_box'] = $request->boolean('has_box');
        $validated['has_cable'] = $request->boolean('has_cable');

        if ($validated['condition'] === 'new') {
            $validated['battery_health'] = null;
            $validated['has_box'] = false;
            $validated['has_cable'] = false;
        }

        return $validated;
    }
}
