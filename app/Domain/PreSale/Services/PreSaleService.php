<?php

declare(strict_types=1);

namespace App\Domain\PreSale\Services;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Marketing\Models\MarketingPrice;
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

    public function markReady(PreSale $preSale): void
    {
        $preSale->update([
            'status' => PreSaleStatus::Ready,
        ]);
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
            $marketingCost = $this->findMarketingCost($product->name, $product->storage);

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
                'cost_price' => $marketingCost ?? (float) $product->cost_price,
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
            $marketingCost = $this->findMarketingCost($consignmentItem->name, $consignmentItem->storage);

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
                'cost_price' => $marketingCost ?? (float) $consignmentItem->supplier_cost,
                'sale_price' => (float) ($consignmentItem->suggested_price ?? $consignmentItem->supplier_cost),
                'reserved' => (bool) ($consignmentItem->reserved ?? false),
                'reserved_by' => $consignmentItem->reserved_by ?? null,
                'supplier_name' => $consignmentItem->supplier?->name,
            ];
        }

        return null;
    }

    public function searchByProductId(string $productId): ?array
    {
        $product = Product::where('id', $productId)
            ->where('active', true)
            ->where('stock_quantity', '>', 0)
            ->first();

        if (!$product) {
            return null;
        }

        $marketingCost = $this->findMarketingCost($product->name, $product->storage);

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
            'cost_price' => $marketingCost ?? (float) $product->cost_price,
            'sale_price' => (float) $product->sale_price,
            'reserved' => (bool) $product->reserved,
            'reserved_by' => $product->reserved_by,
            'sku' => $product->sku,
        ];
    }

    public function searchByConsignmentItemId(string $consignmentItemId): ?array
    {
        $consignmentItem = ConsignmentStockItem::where('id', $consignmentItemId)
            ->where('status', 'available')
            ->where('available_quantity', '>', 0)
            ->with('supplier')
            ->first();

        if (!$consignmentItem) {
            return null;
        }

        $marketingCost = $this->findMarketingCost($consignmentItem->name, $consignmentItem->storage);

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
            'cost_price' => $marketingCost ?? (float) $consignmentItem->supplier_cost,
            'sale_price' => (float) ($consignmentItem->suggested_price ?? $consignmentItem->supplier_cost),
            'reserved' => (bool) ($consignmentItem->reserved ?? false),
            'reserved_by' => $consignmentItem->reserved_by ?? null,
            'supplier_name' => $consignmentItem->supplier?->name,
        ];
    }

    public function searchByName(string $query): array
    {
        $results = [];

        $products = Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(15)
            ->get();

        foreach ($products as $product) {
            $marketingCost = $this->findMarketingCost($product->name, $product->storage);

            $results[] = [
                'source' => 'own_stock',
                'product_id' => $product->id,
                'consignment_item_id' => null,
                'name' => $product->name,
                'model' => $product->model,
                'storage' => $product->storage,
                'color' => $product->color,
                'condition' => $product->condition?->value ?? 'new',
                'imei' => $product->imei,
                'cost_price' => $marketingCost ?? (float) $product->cost_price,
                'sale_price' => (float) $product->sale_price,
                'reserved' => (bool) $product->reserved,
                'reserved_by' => $product->reserved_by,
                'sku' => $product->sku,
            ];
        }

        $consignmentItems = ConsignmentStockItem::where('status', 'available')
            ->where('available_quantity', '>', 0)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            })
            ->with('supplier')
            ->limit(15)
            ->get();

        foreach ($consignmentItems as $item) {
            $marketingCost = $this->findMarketingCost($item->name, $item->storage);

            $results[] = [
                'source' => 'consignment',
                'product_id' => null,
                'consignment_item_id' => $item->id,
                'name' => $item->name,
                'model' => $item->model,
                'storage' => $item->storage,
                'color' => $item->color,
                'condition' => $item->condition?->value ?? 'new',
                'imei' => $item->imei,
                'cost_price' => $marketingCost ?? (float) $item->supplier_cost,
                'sale_price' => (float) ($item->suggested_price ?? $item->supplier_cost),
                'reserved' => (bool) ($item->reserved ?? false),
                'reserved_by' => $item->reserved_by ?? null,
                'supplier_name' => $item->supplier?->name,
            ];
        }

        return $results;
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

    public function getMarketingCost(?string $productName, ?string $storage): ?float
    {
        return $this->findMarketingCost($productName, $storage);
    }

    private function findMarketingCost(?string $productName, ?string $storage): ?float
    {
        if (!$productName) {
            return null;
        }

        $query = MarketingPrice::where('active', true)
            ->whereNotNull('cost_price')
            ->where('cost_price', '>', 0);

        // Extrair palavras relevantes do nome (ignora "Apple")
        $keywords = collect(explode(' ', $productName))
            ->filter(fn ($part) => strlen($part) >= 2 && strtolower($part) !== 'apple')
            ->values();

        if ($keywords->isEmpty()) {
            return null;
        }

        // 1) Busca exata: nome + storage (campo separado ou dentro do nome)
        if ($storage) {
            $match = (clone $query)
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->where('name', 'like', '%' . $kw . '%');
                    }
                })
                ->where(function ($sq) use ($storage) {
                    $sq->where('storage', $storage)
                       ->orWhere('name', 'like', '%' . $storage . '%');
                })
                ->first();

            if ($match) {
                return (float) $match->cost_price;
            }
        }

        // 2) Fallback sem storage
        $match = (clone $query)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->where('name', 'like', '%' . $kw . '%');
                }
            })
            ->first();

        return $match ? (float) $match->cost_price : null;
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
