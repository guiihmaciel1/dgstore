<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function find(string $id): ?Product
    {
        return Product::find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function findByImei(string $imei): ?Product
    {
        return Product::where('imei', $imei)->first();
    }

    public function all(): Collection
    {
        return Product::orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Product::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('imei', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (!empty($filters['model'])) {
            $query->where('model', $filters['model']);
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['low_stock'])) {
            $query->lowStock();
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    public function create(ProductData $data): Product
    {
        return Product::create($data->toArray());
    }

    public function update(Product $product, ProductData $data): Product
    {
        $product->update($data->toArray());
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function restore(Product $product): bool
    {
        return (bool) $product->restore();
    }

    public function getLowStock(): Collection
    {
        return Product::active()
            ->lowStock()
            ->orderBy('stock_quantity')
            ->get();
    }

    public function getActive(): Collection
    {
        return Product::active()
            ->orderBy('name')
            ->get();
    }

    public function search(string $term): Collection
    {
        return Product::where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('imei', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%");
        })
            ->active()
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function updateStock(Product $product, int $quantity): Product
    {
        $product->update(['stock_quantity' => $quantity]);
        return $product->fresh();
    }

    public function decrementStock(Product $product, int $quantity): Product
    {
        $product->decrement('stock_quantity', $quantity);
        return $product->fresh();
    }

    public function incrementStock(Product $product, int $quantity): Product
    {
        $product->increment('stock_quantity', $quantity);
        return $product->fresh();
    }
}
