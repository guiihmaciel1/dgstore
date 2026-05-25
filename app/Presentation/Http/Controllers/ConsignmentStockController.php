<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\ConsignmentStock\Config\StandardColors;
use App\Domain\ConsignmentStock\Enums\ConsignmentMovementType;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Models\ConsignmentStockMovement;
use Illuminate\Support\Facades\DB;
use App\Domain\ConsignmentStock\Services\ConsignmentExchangeService;
use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Domain\ConsignmentStock\Services\ProductCatalogService;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsignmentStockController extends Controller
{
    public function __construct(
        private readonly ConsignmentStockService $service,
        private readonly ProductCatalogService $catalogService,
        private readonly ConsignmentExchangeService $exchangeService,
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

        $soldMovements = ConsignmentStockMovement::where('type', ConsignmentMovementType::Out);

        $stats = [
            'available' => ConsignmentStockItem::available()->sum('available_quantity'),
            'available_value' => ConsignmentStockItem::available()->sum(DB::raw('available_quantity * supplier_cost')),
            'sold' => (clone $soldMovements)->sum('quantity'),
            'sold_value' => (float) (clone $soldMovements)
                ->join('consignment_stock_items', 'consignment_stock_movements.consignment_item_id', '=', 'consignment_stock_items.id')
                ->selectRaw('COALESCE(SUM(consignment_stock_movements.quantity * consignment_stock_items.supplier_cost), 0) as total')
                ->value('total'),
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

    // ───────────────────────────────────────────────────────────
    //  Entrada Inteligente em Lote (multi-IMEI)
    // ───────────────────────────────────────────────────────────

    public function batchCreate(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $defaultSupplierId = $suppliers->count() === 1 ? $suppliers->first()->id : null;

        return view('stock.consignment.batch-create', [
            'suppliers' => $suppliers,
            'defaultSupplierId' => $defaultSupplierId,
        ]);
    }

    public function batchStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:50'],
            'condition' => ['required', 'in:new,used'],
            'received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.color' => ['nullable', 'string', 'max:100'],
            'units.*.imei' => ['nullable', 'string', 'max:50'],
            'units.*.serial_number' => ['nullable', 'string', 'max:100'],
            'units.*.supplier_cost' => ['required', 'numeric', 'min:0'],
            'units.*.suggested_price' => ['nullable', 'numeric', 'min:0'],
            'units.*.battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'units.*.has_box' => ['nullable'],
            'units.*.has_cable' => ['nullable'],
            'units.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $units = $this->normalizeUnits($validated['units'], $validated['condition']);
        $this->ensureUniqueImeis($units);
        $this->validateStandardColors($units, $validated['name']);

        $batchData = collect($validated)->except('units')->toArray();
        $batch = $this->service->registerBatchEntry($batchData, $units, auth()->id());

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', "Lote {$batch->batch_code} cadastrado com sucesso! {$batch->items->count()} unidade(s).");
    }

    public function productCatalog(Request $request): JsonResponse
    {
        $term = (string) $request->get('q', '');
        $limit = (int) $request->get('limit', 20);

        $results = $this->catalogService->searchByTerm($term, $limit);

        return response()->json($results);
    }

    public function validateImei(Request $request): JsonResponse
    {
        $imei = $request->get('imei');
        $serialNumber = $request->get('serial_number');
        $excludeId = $request->get('exclude_id');

        $exists = $this->service->imeiOrSerialExists(
            imei: $imei ? trim($imei) : null,
            serialNumber: $serialNumber ? trim($serialNumber) : null,
            excludeItemId: $excludeId ? (string) $excludeId : null,
        );

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Este IMEI/Serial ja esta cadastrado no estoque consignado.' : 'Disponivel.',
        ]);
    }

    /**
     * Normaliza os booleans de cada unidade e zera bateria/caixa/cabo se for novo.
     */
    private function normalizeUnits(array $units, string $condition): array
    {
        $normalized = [];

        foreach ($units as $unit) {
            $imei = isset($unit['imei']) ? trim((string) $unit['imei']) : null;
            $serial = isset($unit['serial_number']) ? trim((string) $unit['serial_number']) : null;

            $normalized[] = [
                'color' => isset($unit['color']) ? trim((string) $unit['color']) : null,
                'imei' => $imei !== '' ? $imei : null,
                'serial_number' => $serial !== '' ? $serial : null,
                'supplier_cost' => isset($unit['supplier_cost']) ? (float) $unit['supplier_cost'] : 0,
                'suggested_price' => isset($unit['suggested_price']) ? (float) $unit['suggested_price'] : null,
                'battery_health' => $condition === 'used' ? ($unit['battery_health'] ?? null) : null,
                'has_box' => $condition === 'used' ? !empty($unit['has_box']) : false,
                'has_cable' => $condition === 'used' ? !empty($unit['has_cable']) : false,
                'notes' => isset($unit['notes']) ? trim((string) $unit['notes']) : null,
            ];
        }

        return $normalized;
    }

    /**
     * Garante que IMEIs/Seriais nao se repetem no lote nem no banco.
     *
     * @throws ValidationException
     */
    private function ensureUniqueImeis(array $units): void
    {
        $imeis = array_filter(array_column($units, 'imei'));
        $serials = array_filter(array_column($units, 'serial_number'));

        if (count($imeis) !== count(array_unique($imeis))) {
            throw ValidationException::withMessages([
                'units' => 'Existem IMEIs duplicados no lote.',
            ]);
        }

        if (count($serials) !== count(array_unique($serials))) {
            throw ValidationException::withMessages([
                'units' => 'Existem Seriais duplicados no lote.',
            ]);
        }

        foreach ($units as $idx => $unit) {
            if ($this->service->imeiOrSerialExists($unit['imei'], $unit['serial_number'])) {
                throw ValidationException::withMessages([
                    "units.{$idx}.imei" => 'Este IMEI/Serial ja existe no estoque consignado.',
                ]);
            }
        }
    }

    // ───────────────────────────────────────────────────────────
    //  Sistema de Troca com outros lojistas
    // ───────────────────────────────────────────────────────────

    public function exchangeForm(ConsignmentStockItem $item): View
    {
        if (!$item->isAvailable()) {
            abort(403, 'Apenas itens disponiveis podem ser trocados.');
        }

        $item->load('supplier', 'batch', 'exchanges.user');

        return view('stock.consignment.exchange', [
            'item' => $item,
        ]);
    }

    public function exchangeStore(Request $request, ConsignmentStockItem $item): RedirectResponse
    {
        if (!$item->isAvailable()) {
            return redirect()->back()->with('error', 'Apenas itens disponiveis podem ser trocados.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:new,used'],
            'imei' => ['nullable', 'string', 'max:50'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'partner_name' => ['required', 'string', 'max:255'],
            'cost_adjustment' => ['nullable', 'numeric'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $newData = [
            'imei' => $validated['imei'] ? trim($validated['imei']) : null,
            'serial_number' => $validated['serial_number'] ? trim($validated['serial_number']) : null,
            'name' => $validated['name'],
            'model' => $validated['model'] ?? null,
            'storage' => $validated['storage'] ?? null,
            'color' => $validated['color'] ?? null,
            'condition' => $validated['condition'],
        ];

        if (!$newData['imei'] && !$newData['serial_number']) {
            throw ValidationException::withMessages([
                'imei' => 'Informe ao menos um IMEI ou Serial Number do aparelho recebido.',
            ]);
        }

        try {
            $this->exchangeService->exchange(
                item: $item,
                newData: $newData,
                partnerName: trim($validated['partner_name']),
                costAdjustment: (float) ($validated['cost_adjustment'] ?? 0),
                reason: $validated['reason'] ?? null,
                userId: auth()->id(),
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['imei' => $e->getMessage()]);
        }

        return redirect()
            ->route('stock.consignment.index')
            ->with('success', 'Troca registrada com sucesso! Historico do IMEI preservado.');
    }

    public function history(ConsignmentStockItem $item): View
    {
        $item->load([
            'supplier',
            'batch',
            'exchanges' => fn ($q) => $q->orderByDesc('exchanged_at'),
            'exchanges.user',
            'movements' => fn ($q) => $q->orderByDesc('created_at'),
            'movements.user',
        ]);

        return view('stock.consignment.history', [
            'item' => $item,
        ]);
    }

    /**
     * Valida se as cores dos produtos estão dentro das cores padronizadas.
     * 
     * @param array $units
     * @param string $productName
     * @throws ValidationException
     */
    private function validateStandardColors(array $units, string $productName): void
    {
        $standardColors = StandardColors::getColorsForModel($productName);
        
        if ($standardColors === null) {
            return; // Não há cores padronizadas para este modelo
        }

        foreach ($units as $idx => $unit) {
            $color = $unit['color'] ?? '';
            
            if (empty($color)) {
                continue; // Cor não fornecida, será validado pelas regras principais
            }

            // Normaliza a cor fornecida e as cores padrão para comparação case-insensitive
            $normalizedColor = mb_strtolower(trim($color));
            $normalizedStandardColors = array_map(
                fn($c) => mb_strtolower(trim($c)),
                $standardColors
            );

            if (!in_array($normalizedColor, $normalizedStandardColors, true)) {
                $allowedColors = implode(', ', $standardColors);
                throw ValidationException::withMessages([
                    "units.{$idx}.color" => "Cor '{$color}' não é válida para {$productName}. Cores permitidas: {$allowedColors}"
                ]);
            }
        }
    }
}
