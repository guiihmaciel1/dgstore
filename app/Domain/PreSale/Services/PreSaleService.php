<?php

declare(strict_types=1);

namespace App\Domain\PreSale\Services;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\PreSale\Enums\PreSaleStatus;
use App\Domain\PreSale\Models\PreSale;
use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PreSaleService
{
    public function create(array $data): PreSale
    {
        return DB::transaction(function () use ($data) {
            $preSale = PreSale::create($data);

            $this->reserveProduct($preSale);

            return $preSale;
        });
    }

    public function cancel(PreSale $preSale, ?string $reason = null): void
    {
        DB::transaction(function () use ($preSale, $reason) {
            $preSale->update([
                'status' => PreSaleStatus::Cancelled,
                'cancelled_at' => now(),
                'cancelled_reason' => $reason,
            ]);

            $this->releaseProduct($preSale);
        });
    }

    public function markConverted(PreSale $preSale, string $saleId): void
    {
        $preSale->update([
            'status' => PreSaleStatus::Converted,
            'converted_sale_id' => $saleId,
            'converted_at' => now(),
        ]);

        $this->releaseProduct($preSale);
    }

    public function list(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = PreSale::with(['customer', 'seller']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['seller_id'])) {
            $query->where('seller_id', $filters['seller_id']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
    }

    public function searchByImei(string $imei): ?array
    {
        $product = Product::where('imei', $imei)
            ->where('active', true)
            ->where('stock_quantity', '>', 0)
            ->first();

        if ($product) {
            return [
                'source' => 'own_stock',
                'product_id' => $product->id,
                'consignment_item_id' => null,
                'name' => $product->name,
                'model' => $product->model,
                'storage' => $product->storage,
                'color' => $product->color,
                'condition' => $product->condition?->value ?? 'new',
                'imei' => $product->imei,
                'cost_price' => (float) $product->cost_price,
                'sale_price' => (float) $product->sale_price,
                'reserved' => (bool) $product->reserved,
                'reserved_by' => $product->reserved_by,
                'sku' => $product->sku,
            ];
        }

        $consignmentItem = ConsignmentStockItem::where('imei', $imei)
            ->where('status', 'available')
            ->where('available_quantity', '>', 0)
            ->with('supplier')
            ->first();

        if ($consignmentItem) {
            return [
                'source' => 'consignment',
                'product_id' => null,
                'consignment_item_id' => $consignmentItem->id,
                'name' => $consignmentItem->name,
                'model' => $consignmentItem->model,
                'storage' => $consignmentItem->storage,
                'color' => $consignmentItem->color,
                'condition' => $consignmentItem->condition?->value ?? 'new',
                'imei' => $consignmentItem->imei,
                'cost_price' => (float) $consignmentItem->supplier_cost,
                'sale_price' => (float) ($consignmentItem->suggested_price ?? $consignmentItem->supplier_cost),
                'reserved' => (bool) ($consignmentItem->reserved ?? false),
                'reserved_by' => $consignmentItem->reserved_by ?? null,
                'supplier_name' => $consignmentItem->supplier?->name,
            ];
        }

        return null;
    }

    private function reserveProduct(PreSale $preSale): void
    {
        if ($preSale->product_id) {
            Product::where('id', $preSale->product_id)->update([
                'reserved' => true,
                'reserved_by' => $preSale->id,
            ]);
        } elseif ($preSale->consignment_item_id) {
            ConsignmentStockItem::where('id', $preSale->consignment_item_id)->update([
                'reserved' => true,
                'reserved_by' => $preSale->id,
            ]);
        }
    }

    private function releaseProduct(PreSale $preSale): void
    {
        if ($preSale->product_id) {
            Product::where('id', $preSale->product_id)
                ->where('reserved_by', $preSale->id)
                ->update(['reserved' => false, 'reserved_by' => null]);
        }

        if ($preSale->consignment_item_id) {
            ConsignmentStockItem::where('id', $preSale->consignment_item_id)
                ->where('reserved_by', $preSale->id)
                ->update(['reserved' => false, 'reserved_by' => null]);
        }
    }
}
