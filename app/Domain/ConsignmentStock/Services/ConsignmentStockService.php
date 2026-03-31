<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Services;

use App\Domain\ConsignmentStock\Enums\ConsignmentMovementType;
use App\Domain\ConsignmentStock\Enums\ConsignmentStatus;
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

            $item = ConsignmentStockItem::create([
                'supplier_id' => $data['supplier_id'],
                'name' => $data['name'],
                'model' => $data['model'] ?? null,
                'storage' => $data['storage'] ?? null,
                'color' => $data['color'] ?? null,
                'condition' => $data['condition'] ?? 'new',
                'imei' => $data['imei'] ?? null,
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
                'reason' => 'Entrada de estoque consignado',
            ]);

            return $item;
        });
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
        return ConsignmentStockItem::with('supplier')
            ->bySupplier($supplierId)
            ->available()
            ->orderBy('name')
            ->get();
    }

    public function getSoldBySupplier(string $supplierId, ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = ConsignmentStockItem::with('supplier')
            ->bySupplier($supplierId)
            ->sold();

        if ($dateFrom) {
            $query->where('sold_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('sold_at', '<=', $dateTo . ' 23:59:59');
        }

        return $query->orderBy('sold_at', 'desc')->get();
    }

    public function searchAvailable(?string $term): Collection
    {
        return ConsignmentStockItem::with('supplier')
            ->available()
            ->where('available_quantity', '>', 0)
            ->search($term)
            ->orderBy('name')
            ->limit(20)
            ->get();
    }
}
