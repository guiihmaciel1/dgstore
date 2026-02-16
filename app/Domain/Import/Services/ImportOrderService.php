<?php

declare(strict_types=1);

namespace App\Domain\Import\Services;

use App\Domain\Finance\Services\FinanceService;
use App\Domain\Import\Enums\ImportOrderStatus;
use App\Domain\Import\Models\ImportOrder;
use App\Domain\Import\Models\ImportOrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportOrderService
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}
    /**
     * Lista pedidos com filtros
     */
    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = ImportOrder::with(['supplier', 'user', 'items']);

        // Filtro de busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro de status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtro apenas ativos
        if (!empty($filters['active_only'])) {
            $query->active();
        }

        // Ordenação
        $sortField = $filters['sort'] ?? 'ordered_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Cria novo pedido de importação
     */
    public function create(array $data, array $items): ImportOrder
    {
        return DB::transaction(function () use ($data, $items) {
            // Calcula custo estimado dos itens
            $estimatedCost = collect($items)->sum(fn($item) => $item['quantity'] * $item['unit_cost']);

            $order = ImportOrder::create([
                'supplier_id' => $data['supplier_id'] ?? null,
                'user_id' => $data['user_id'],
                'status' => ImportOrderStatus::Ordered,
                'tracking_code' => $data['tracking_code'] ?? null,
                'estimated_cost' => $estimatedCost,
                'exchange_rate' => $data['exchange_rate'] ?? 5.00,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'taxes' => $data['taxes'] ?? 0,
                'ordered_at' => $data['ordered_at'] ?? now(),
                'estimated_arrival' => $data['estimated_arrival'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Cria os itens
            foreach ($items as $item) {
                ImportOrderItem::create([
                    'import_order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                ]);
            }

            // Lança despesa no financeiro (pedido = já pago)
            $totalBrl = $order->estimated_total_brl;
            if ($totalBrl > 0) {
                $this->financeService->registerImportExpense(
                    $data['user_id'],
                    $totalBrl,
                    "Importação #{$order->order_number}",
                    $order->id,
                    $order->ordered_at ? \Carbon\Carbon::parse($order->ordered_at) : now(),
                );
            }

            return $order->load(['supplier', 'items']);
        });
    }

    /**
     * Atualiza status do pedido
     */
    public function updateStatus(ImportOrder $order, ImportOrderStatus $newStatus, ?array $data = []): ImportOrder
    {
        $updateData = ['status' => $newStatus];

        if ($newStatus === ImportOrderStatus::Shipped) {
            $updateData['shipped_at'] = $data['shipped_at'] ?? now();
            if (!empty($data['tracking_code'])) {
                $updateData['tracking_code'] = $data['tracking_code'];
            }
        }

        if ($newStatus === ImportOrderStatus::Received) {
            $updateData['received_at'] = $data['received_at'] ?? now();
            if (isset($data['actual_cost'])) {
                $updateData['actual_cost'] = $data['actual_cost'];
            }
        }

        $order->update($updateData);

        return $order->fresh();
    }

    /**
     * Recebe pedido (marca como recebido e atualiza itens)
     */
    public function receive(ImportOrder $order, array $receivedItems, ?float $actualCost = null): ImportOrder
    {
        return DB::transaction(function () use ($order, $receivedItems, $actualCost) {
            // Atualiza quantidades recebidas
            foreach ($receivedItems as $itemId => $receivedQty) {
                $item = $order->items()->find($itemId);
                if ($item) {
                    $item->update(['received_quantity' => $receivedQty]);
                }
            }

            // Atualiza status e custos
            $order->update([
                'status' => ImportOrderStatus::Received,
                'received_at' => now(),
                'actual_cost' => $actualCost ?? $order->estimated_cost,
            ]);

            return $order->fresh(['items']);
        });
    }

    /**
     * Retorna pedidos em trânsito
     */
    public function getInTransit(): Collection
    {
        return ImportOrder::with(['supplier', 'items'])
            ->inTransit()
            ->orderBy('estimated_arrival')
            ->get();
    }

    /**
     * Conta pedidos em trânsito
     */
    public function countInTransit(): int
    {
        return ImportOrder::inTransit()->count();
    }

    /**
     * Conta pedidos ativos (não recebidos/cancelados)
     */
    public function countActive(): int
    {
        return ImportOrder::active()->count();
    }

    /**
     * Retorna estatísticas de custos
     */
    public function getCostStats(): array
    {
        $received = ImportOrder::where('status', ImportOrderStatus::Received)
            ->whereNotNull('actual_cost')
            ->get();

        if ($received->isEmpty()) {
            return [
                'total_estimated' => 0,
                'total_actual' => 0,
                'difference' => 0,
                'difference_percent' => 0,
            ];
        }

        $totalEstimated = $received->sum('estimated_total_brl');
        $totalActual = $received->sum('actual_total_brl');

        return [
            'total_estimated' => $totalEstimated,
            'total_actual' => $totalActual,
            'difference' => $totalActual - $totalEstimated,
            'difference_percent' => $totalEstimated > 0 
                ? (($totalActual - $totalEstimated) / $totalEstimated) * 100 
                : 0,
        ];
    }

    /**
     * Cancela pedido e reverte lançamento financeiro
     */
    public function cancel(ImportOrder $order): ImportOrder
    {
        return DB::transaction(function () use ($order) {
            $order->update(['status' => ImportOrderStatus::Cancelled]);

            $this->financeService->cancelImportTransactions($order->id);

            return $order->fresh();
        });
    }
}
