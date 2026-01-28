<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Import\Enums\ImportOrderStatus;
use App\Domain\Import\Models\ImportOrder;
use App\Domain\Import\Services\ImportOrderService;
use App\Domain\Supplier\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportOrderController extends Controller
{
    public function __construct(
        private readonly ImportOrderService $importService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'active_only' => $request->boolean('active_only'),
            'sort' => $request->get('sort', 'ordered_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        $orders = $this->importService->list(15, $filters);

        $stats = [
            'in_transit' => $this->importService->countInTransit(),
            'active' => $this->importService->countActive(),
        ];

        return view('imports.index', [
            'orders' => $orders,
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => ImportOrderStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('imports.create', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
            'exchange_rate' => ['required', 'numeric', 'min:0.01'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'taxes' => ['nullable', 'numeric', 'min:0'],
            'ordered_at' => ['required', 'date'],
            'estimated_arrival' => ['nullable', 'date', 'after_or_equal:ordered_at'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0.01'],
        ]);

        try {
            $order = $this->importService->create(
                array_merge($request->only([
                    'supplier_id', 'tracking_code', 'exchange_rate',
                    'shipping_cost', 'taxes', 'ordered_at', 'estimated_arrival', 'notes'
                ]), ['user_id' => auth()->id()]),
                $request->items
            );

            return redirect()
                ->route('imports.show', $order)
                ->with('success', "Pedido #{$order->order_number} criado com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(ImportOrder $import): View
    {
        $import->load(['supplier', 'user', 'items.product']);

        return view('imports.show', [
            'order' => $import,
            'statuses' => ImportOrderStatus::cases(),
        ]);
    }

    public function updateStatus(Request $request, ImportOrder $import): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:ordered,shipped,in_transit,customs,received,cancelled'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $newStatus = ImportOrderStatus::from($request->status);

            if (!$import->canAdvanceTo($newStatus)) {
                return redirect()
                    ->back()
                    ->with('error', 'Não é possível alterar para este status.');
            }

            $this->importService->updateStatus($import, $newStatus, [
                'tracking_code' => $request->tracking_code,
            ]);

            return redirect()
                ->route('imports.show', $import)
                ->with('success', 'Status atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function receive(ImportOrder $import): View
    {
        if ($import->status === ImportOrderStatus::Received) {
            return redirect()
                ->route('imports.show', $import)
                ->with('error', 'Este pedido já foi recebido.');
        }

        $import->load(['supplier', 'items.product']);

        return view('imports.receive', [
            'order' => $import,
        ]);
    }

    public function confirmReceive(Request $request, ImportOrder $import): RedirectResponse
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['required', 'integer', 'min:0'],
            'actual_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $this->importService->receive(
                $import,
                $request->items,
                $request->actual_cost
            );

            return redirect()
                ->route('imports.show', $import)
                ->with('success', 'Pedido recebido com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function cancel(ImportOrder $import): RedirectResponse
    {
        try {
            if ($import->status === ImportOrderStatus::Received) {
                return redirect()
                    ->back()
                    ->with('error', 'Não é possível cancelar um pedido já recebido.');
            }

            $this->importService->cancel($import);

            return redirect()
                ->route('imports.show', $import)
                ->with('success', 'Pedido cancelado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
