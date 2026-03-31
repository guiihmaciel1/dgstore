<?php

declare(strict_types=1);

namespace App\Domain\Product\Services;

use App\Domain\Marketing\Models\MarketingResaleItem;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Enums\ProductCondition;
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
        $product = $this->repository->create($data);
        $this->syncMarketingListings($product);
        return $product;
    }

    public function update(Product $product, ProductData $data): Product
    {
        $product = $this->repository->update($product, $data);
        $this->syncMarketingListings($product);
        return $product;
    }

    private function syncMarketingListings(Product $product): void
    {
        if (!in_array($product->condition, [ProductCondition::Used, ProductCondition::Refurbished])) {
            return;
        }

        $hasPricing = $product->cost_price || $product->sale_price || $product->resale_price;
        if (!$hasPricing) {
            return;
        }

        MarketingUsedListing::updateOrCreate(
            [
                'listable_type' => Product::class,
                'listable_id' => $product->id,
            ],
            array_filter([
                'cost_price' => $product->cost_price,
                'final_price' => $product->sale_price,
                'battery_health' => $product->battery_health,
                'has_box' => $product->has_box ?? false,
                'has_cable' => $product->has_cable ?? false,
            ], fn ($v) => $v !== null)
        );

        if ($product->resale_price) {
            MarketingResaleItem::updateOrCreate(
                [
                    'resaleable_type' => Product::class,
                    'resaleable_id' => $product->id,
                ],
                array_filter([
                    'resale_price' => $product->resale_price,
                    'battery_health' => $product->battery_health,
                    'has_box' => $product->has_box ?? false,
                    'has_cable' => $product->has_cable ?? false,
                ], fn ($v) => $v !== null)
            );
        }
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
