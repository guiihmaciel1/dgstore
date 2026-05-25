<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Services;

use App\Domain\ConsignmentStock\Enums\ConsignmentMovementType;
use App\Domain\ConsignmentStock\Enums\ConsignmentStatus;
use App\Domain\ConsignmentStock\Models\ConsignmentBatch;
use App\Domain\ConsignmentStock\Models\ConsignmentPriceHistory;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\ConsignmentStock\Models\ConsignmentStockMovement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConsignmentStockService
{
    public function registerEntry(array $data, string $userId): ConsignmentStockItem
    {
        return DB::transaction(function () use ($data, $userId) {
            $quantity = (int) ($data['quantity'] ?? 1);

            $batch = ConsignmentBatch::create([
                'supplier_id' => $data['supplier_id'],
                'name' => $data['name'],
                'model' => $data['model'] ?? null,
                'storage' => $data['storage'] ?? null,
                'color' => $data['color'] ?? null,
                'condition' => $data['condition'] ?? 'new',
                'supplier_cost' => $data['supplier_cost'],
                'suggested_price' => $data['suggested_price'] ?? null,
                'total_quantity' => $quantity,
                'notes' => $data['notes'] ?? null,
                'received_at' => $data['received_at'] ?? now(),
            ]);

            $item = ConsignmentStockItem::create([
                'supplier_id' => $data['supplier_id'],
                'batch_id' => $batch->id,
                'name' => $data['name'],
                'model' => $data['model'] ?? null,
                'storage' => $data['storage'] ?? null,
                'color' => $data['color'] ?? null,
                'condition' => $data['condition'] ?? 'new',
                'battery_health' => $data['battery_health'] ?? null,
                'has_box' => $data['has_box'] ?? false,
                'has_cable' => $data['has_cable'] ?? false,
                'imei' => $data['imei'] ?? null,
                'serial_number' => $data['serial_number'] ?? null,
                'supplier_cost' => $data['supplier_cost'],
                'suggested_price' => $data['suggested_price'] ?? null,
                'quantity' => $quantity,
                'available_quantity' => $quantity,
                'status' => ConsignmentStatus::Available,
                'notes' => $data['notes'] ?? null,
                'received_at' => $data['received_at'] ?? now(),
            ]);

            ConsignmentStockMovement::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'type' => ConsignmentMovementType::In,
                'quantity' => $quantity,
                'reason' => 'Entrada de estoque consignado - Lote ' . $batch->batch_code,
            ]);

            return $item;
        });
    }

    /**
     * Registra uma entrada em lote com multiplas unidades, cada uma com seu IMEI/Serial proprio.
     *
     * Cada unidade vira um ConsignmentStockItem com quantity=1 (rastreabilidade individual).
     * O batch e compartilhado entre todas as unidades do lote.
     *
     * @param  array  $batchData  dados comuns do lote (supplier_id, name, model, storage, condition, supplier_cost, suggested_price, received_at, notes)
     * @param  array  $units      lista de unidades, cada uma com (color, imei, serial_number, battery_health, has_box, has_cable, notes)
     * @param  string|null  $userId  ID do usuario interno (users). Null quando acao vem do portal do fornecedor.
     * @return ConsignmentBatch   batch criado com items carregados
     */
    public function registerBatchEntry(array $batchData, array $units, ?string $userId = null): ConsignmentBatch
    {
        if (empty($units)) {
            throw new \InvalidArgumentException('E necessario informar ao menos 1 unidade no lote.');
        }

        return DB::transaction(function () use ($batchData, $units, $userId) {
            // Calcula o custo médio do lote baseado nas unidades
            $avgCost = !empty($units) ? array_sum(array_column($units, 'supplier_cost')) / count($units) : 0;
            $avgPrice = !empty($units) && array_filter(array_column($units, 'suggested_price')) 
                ? array_sum(array_filter(array_column($units, 'suggested_price'))) / count(array_filter(array_column($units, 'suggested_price'))) 
                : null;

            $batch = ConsignmentBatch::create([
                'supplier_id' => $batchData['supplier_id'],
                'name' => $batchData['name'],
                'model' => $batchData['model'] ?? null,
                'storage' => $batchData['storage'] ?? null,
                'color' => null, // batch nao tem cor unica - cada unit tem a sua
                'condition' => $batchData['condition'] ?? 'new',
                'supplier_cost' => $avgCost, // custo médio do lote
                'suggested_price' => $avgPrice, // preço médio do lote
                'total_quantity' => count($units),
                'notes' => $batchData['notes'] ?? null,
                'received_at' => $batchData['received_at'] ?? now(),
            ]);

            $movementReason = 'Entrada de estoque consignado - Lote ' . $batch->batch_code;
            if ($userId === null && !empty($batchData['registered_by'])) {
                $movementReason .= ' (Portal: ' . $batchData['registered_by'] . ')';
            }

            foreach ($units as $unit) {
                $item = ConsignmentStockItem::create([
                    'supplier_id' => $batchData['supplier_id'],
                    'batch_id' => $batch->id,
                    'name' => $batchData['name'],
                    'model' => $batchData['model'] ?? null,
                    'storage' => $batchData['storage'] ?? null,
                    'color' => $unit['color'] ?? null,
                    'condition' => $batchData['condition'] ?? 'new',
                    'battery_health' => $unit['battery_health'] ?? null,
                    'has_box' => $unit['has_box'] ?? false,
                    'has_cable' => $unit['has_cable'] ?? false,
                    'imei' => $unit['imei'] ?? null,
                    'serial_number' => $unit['serial_number'] ?? null,
                    'supplier_cost' => $unit['supplier_cost'] ?? 0, // custo individual da unidade
                    'suggested_price' => $unit['suggested_price'] ?? null, // preço individual da unidade
                    'quantity' => 1,
                    'available_quantity' => 1,
                    'status' => ConsignmentStatus::Available,
                    'notes' => $unit['notes'] ?? null,
                    'received_at' => $batchData['received_at'] ?? now(),
                ]);

                ConsignmentStockMovement::create([
                    'consignment_item_id' => $item->id,
                    'user_id' => $userId,
                    'type' => ConsignmentMovementType::In,
                    'quantity' => 1,
                    'reason' => $movementReason,
                ]);
            }

            return $batch->load('items');
        });
    }

    /**
     * Verifica se um IMEI/Serial ja existe no estoque consignado.
     */
    public function imeiOrSerialExists(?string $imei, ?string $serialNumber, ?string $excludeItemId = null): bool
    {
        if (!$imei && !$serialNumber) {
            return false;
        }

        $query = ConsignmentStockItem::query();

        $query->where(function ($q) use ($imei, $serialNumber) {
            if ($imei) {
                $q->orWhere('imei', $imei);
            }
            if ($serialNumber) {
                $q->orWhere('serial_number', $serialNumber);
            }
        });

        if ($excludeItemId) {
            $query->where('id', '!=', $excludeItemId);
        }

        return $query->exists();
    }

    /**
     * Detecta itens disponíveis do mesmo produto/fornecedor com preço diferente.
     *
     * @return Collection<ConsignmentStockItem> Itens com preço divergente
     */
    public function detectPriceDivergence(array $data): Collection
    {
        return ConsignmentStockItem::where('supplier_id', $data['supplier_id'])
            ->where('status', ConsignmentStatus::Available)
            ->where('available_quantity', '>', 0)
            ->where('name', $data['name'])
            ->where(function ($q) use ($data) {
                $q->where('model', $data['model'] ?? null);
                if (empty($data['model'])) {
                    $q->orWhereNull('model');
                }
            })
            ->where(function ($q) use ($data) {
                $q->where('storage', $data['storage'] ?? null);
                if (empty($data['storage'])) {
                    $q->orWhereNull('storage');
                }
            })
            ->where(function ($q) use ($data) {
                $q->where('color', $data['color'] ?? null);
                if (empty($data['color'])) {
                    $q->orWhereNull('color');
                }
            })
            ->where('supplier_cost', '!=', $data['supplier_cost'])
            ->with('supplier', 'batch')
            ->orderByDesc('received_at')
            ->get();
    }

    /**
     * Atualiza preços de itens existentes com base em um novo lote,
     * registrando o histórico completo.
     */
    public function updatePricesFromBatch(
        ConsignmentBatch $batch,
        Collection $itemsToUpdate,
        string $reason,
        string $userId,
    ): ConsignmentPriceHistory {
        return DB::transaction(function () use ($batch, $itemsToUpdate, $reason, $userId) {
            $oldCost = (float) $itemsToUpdate->first()->supplier_cost;
            $oldSuggested = $itemsToUpdate->first()->suggested_price
                ? (float) $itemsToUpdate->first()->suggested_price
                : null;

            $itemIds = $itemsToUpdate->pluck('id')->toArray();

            ConsignmentStockItem::whereIn('id', $itemIds)->update([
                'supplier_cost' => $batch->supplier_cost,
                'suggested_price' => $batch->suggested_price,
            ]);

            return ConsignmentPriceHistory::create([
                'batch_id' => $batch->id,
                'user_id' => $userId,
                'old_supplier_cost' => $oldCost,
                'new_supplier_cost' => $batch->supplier_cost,
                'old_suggested_price' => $oldSuggested,
                'new_suggested_price' => $batch->suggested_price,
                'reason' => $reason,
                'affected_items_count' => count($itemIds),
                'affected_item_ids' => $itemIds,
            ]);
        });
    }

    /**
     * Retorna o histórico de alterações de preço que afetaram um item específico.
     */
    public function getPriceHistoryForItem(string $itemId): Collection
    {
        return ConsignmentPriceHistory::with('user', 'batch')
            ->whereJsonContains('affected_item_ids', $itemId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function registerSaleExit(ConsignmentStockItem $item, string $saleId, string $userId, int $quantity = 1): void
    {
        DB::transaction(function () use ($item, $saleId, $userId, $quantity) {
            $item->update([
                'available_quantity' => max(0, $item->available_quantity - $quantity),
                'status' => ($item->available_quantity - $quantity) <= 0
                    ? ConsignmentStatus::Sold
                    : ConsignmentStatus::Available,
                'sold_at' => ($item->available_quantity - $quantity) <= 0 ? now() : $item->sold_at,
                'sale_id' => $saleId,
            ]);

            ConsignmentStockMovement::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'type' => ConsignmentMovementType::Out,
                'quantity' => $quantity,
                'reason' => 'Saída por venda',
                'reference_id' => $saleId,
            ]);
        });
    }

    public function registerReturn(ConsignmentStockItem $item, string $userId, ?string $reason = null): void
    {
        DB::transaction(function () use ($item, $userId, $reason) {
            $item->update([
                'status' => ConsignmentStatus::Returned,
                'available_quantity' => 0,
            ]);

            ConsignmentStockMovement::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'type' => ConsignmentMovementType::Return,
                'quantity' => $item->quantity,
                'reason' => $reason ?? 'Devolução ao fornecedor',
            ]);
        });
    }

    public function reverseSaleExit(ConsignmentStockItem $item, string $userId, int $quantity = 1): void
    {
        DB::transaction(function () use ($item, $userId, $quantity) {
            $item->update([
                'status' => ConsignmentStatus::Available,
                'available_quantity' => $item->available_quantity + $quantity,
                'sold_at' => null,
                'sale_id' => null,
            ]);

            ConsignmentStockMovement::create([
                'consignment_item_id' => $item->id,
                'user_id' => $userId,
                'type' => ConsignmentMovementType::In,
                'quantity' => $quantity,
                'reason' => 'Cancelamento de venda - devolução ao estoque consignado',
            ]);
        });
    }

    public function getAvailableBySupplier(string $supplierId): Collection
    {
        return ConsignmentStockItem::with('supplier', 'batch')
            ->bySupplier($supplierId)
            ->available()
            ->orderBy('name')
            ->get();
    }

    public function getSoldBySupplier(string $supplierId, ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = ConsignmentStockMovement::with('consignmentItem.supplier', 'consignmentItem.batch')
            ->where('type', ConsignmentMovementType::Out)
            ->whereHas('consignmentItem', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            });

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function searchAvailable(?string $term): Collection
    {
        return ConsignmentStockItem::with('supplier', 'batch')
            ->available()
            ->where('available_quantity', '>', 0)
            ->search($term)
            ->orderBy('name')
            ->limit(20)
            ->get();
    }
}
