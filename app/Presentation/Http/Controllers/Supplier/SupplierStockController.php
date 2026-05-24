<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierStockController extends Controller
{
    public function __construct(
        private ConsignmentStockService $consignmentService
    ) {}

    public function index(Request $request): View
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        $search = $request->input('search');
        $status = $request->input('status', 'available');

        $query = ConsignmentStockItem::bySupplier($supplierId);

        if ($search) {
            $query->search($search);
        }

        match ($status) {
            'sold' => $query->sold(),
            'returned' => $query->returned(),
            default => $query->available(),
        };

        $items = $query->with('batch')->latest()->paginate(20);

        return view('supplier.stock.index', compact('items', 'search', 'status'));
    }

    public function batchCreate(): View
    {
        return view('supplier.stock.batch-create');
    }

    public function batchStore(Request $request): RedirectResponse
    {
        $supplierId = auth('supplier')->user()->supplier_id;
        
        $request->validate([
            'received_at' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'units' => 'required|array|min:1',
            'units.*.product_name' => 'required|string',
            'units.*.model' => 'nullable|string',
            'units.*.storage' => 'nullable|string',
            'units.*.color' => 'required|string',
            'units.*.condition' => 'required|in:new,used,refurbished',
            'units.*.imei' => 'nullable|string|max:20',
            'units.*.serial_number' => 'nullable|string|max:255',
            'units.*.supplier_cost' => 'required|numeric|min:0',
            'units.*.suggested_price' => 'nullable|numeric|min:0',
            'units.*.battery_health' => 'nullable|integer|min:0|max:100',
            'units.*.has_box' => 'nullable|boolean',
            'units.*.has_cable' => 'nullable|boolean',
        ]);

        $units = $this->normalizeUnits($request->input('units'));
        $this->ensureUniqueImeis($units);

        try {
            $batchData = [
                'supplier_id' => $supplierId,
                'received_at' => $request->input('received_at'),
                'invoice_number' => $request->input('invoice_number'),
                'notes' => $request->input('notes'),
            ];

            DB::beginTransaction();
            
            $batch = $this->consignmentService->registerBatchEntry(
                $batchData,
                $units,
                auth('supplier')->id()
            );

            DB::commit();

            return redirect()
                ->route('supplier.stock.index')
                ->with('success', "Lote {$batch->batch_code} cadastrado com sucesso! {$batch->items_count} item(ns) adicionado(s).");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar lote: ' . $e->getMessage()]);
        }
    }

    public function show(ConsignmentStockItem $item): View
    {
        if ($item->supplier_id !== auth('supplier')->user()->supplier_id) {
            abort(403, 'Acesso negado.');
        }

        $item->load(['batch', 'movements.user', 'sale']);

        return view('supplier.stock.show', compact('item'));
    }

    public function edit(ConsignmentStockItem $item): View
    {
        if ($item->supplier_id !== auth('supplier')->user()->supplier_id) {
            abort(403, 'Acesso negado.');
        }

        return view('supplier.stock.edit', compact('item'));
    }

    public function update(Request $request, ConsignmentStockItem $item): RedirectResponse
    {
        if ($item->supplier_id !== auth('supplier')->user()->supplier_id) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'supplier_cost' => 'required|numeric|min:0',
            'suggested_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item->update($request->only('supplier_cost', 'suggested_price', 'notes'));

        return redirect()
            ->route('supplier.stock.show', $item)
            ->with('success', 'Item atualizado com sucesso!');
    }

    private function normalizeUnits(array $units): array
    {
        return array_map(function ($unit) {
            $unit['quantity'] = 1;
            $unit['imei'] = !empty($unit['imei']) ? $unit['imei'] : null;
            $unit['serial_number'] = !empty($unit['serial_number']) ? $unit['serial_number'] : null;
            $unit['battery_health'] = !empty($unit['battery_health']) ? (int) $unit['battery_health'] : null;
            $unit['has_box'] = isset($unit['has_box']) ? (bool) $unit['has_box'] : false;
            $unit['has_cable'] = isset($unit['has_cable']) ? (bool) $unit['has_cable'] : false;
            $unit['suggested_price'] = !empty($unit['suggested_price']) ? $unit['suggested_price'] : null;
            
            $unit['name'] = $unit['product_name'];
            unset($unit['product_name']);
            
            return $unit;
        }, $units);
    }

    private function ensureUniqueImeis(array $units): void
    {
        $imeis = array_filter(array_column($units, 'imei'));
        $serials = array_filter(array_column($units, 'serial_number'));

        if (count($imeis) !== count(array_unique($imeis))) {
            throw new \Exception('IMEIs duplicados no lote.');
        }

        if (count($serials) !== count(array_unique($serials))) {
            throw new \Exception('Serials duplicados no lote.');
        }

        foreach ($units as $unit) {
            if ($this->consignmentService->imeiOrSerialExists($unit['imei'], $unit['serial_number'])) {
                $identifier = $unit['imei'] ?? $unit['serial_number'];
                throw new \Exception("IMEI/Serial {$identifier} já existe no sistema.");
            }
        }
    }
}
