<?php

declare(strict_types=1);

namespace App\Domain\Product\Services;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {}

    public function find(string $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return $this->repository->findBySku($sku);
    }

    public function findByImei(string $imei): ?Product
    {
        return $this->repository->findByImei($imei);
    }

    public function list(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function create(ProductData $data): Product
    {
        return $this->repository->create($data);
    }

    public function update(Product $product, ProductData $data): Product
    {
        return $this->repository->update($product, $data);
    }

    public function delete(Product $product): bool
    {
        return $this->repository->delete($product);
    }

    public function restore(Product $product): bool
    {
        return $this->repository->restore($product);
    }

    public function getLowStockProducts(): Collection
    {
        return $this->repository->getLowStock();
    }

    public function getActiveProducts(): Collection
    {
        return $this->repository->getActive();
    }

    public function active(): Collection
    {
        return $this->repository->getActive();
    }

    public function search(string $term): Collection
    {
        return $this->repository->search($term);
    }

    public function adjustStock(Product $product, int $quantity): Product
    {
        return $this->repository->updateStock($product, $quantity);
    }

    public function decrementStock(Product $product, int $quantity): Product
    {
        return $this->repository->decrementStock($product, $quantity);
    }

    public function incrementStock(Product $product, int $quantity): Product
    {
        return $this->repository->incrementStock($product, $quantity);
    }

    public function checkStockAvailability(string $productId, int $quantity): bool
    {
        $product = $this->repository->find($productId);
        
        if (!$product) {
            return false;
        }

        return $product->stock_quantity >= $quantity;
    }

    public function generateSku(string $category, string $model = ''): string
    {
        $prefix = match ($category) {
            'iphone' => 'IPH',
            'accessory' => 'ACC',
            'service' => 'SRV',
            default => 'PRD',
        };

        $modelCode = $model ? strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $model), 0, 4)) : '';
        $random = strtoupper(substr(uniqid(), -5));

        return "{$prefix}{$modelCode}{$random}";
    }
}
