<?php

declare(strict_types=1);

namespace App\Domain\Product\Repositories;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function find(string $id): ?Product;

    public function findBySku(string $sku): ?Product;

    public function findByImei(string $imei): ?Product;

    public function all(): Collection;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(ProductData $data): Product;

    public function update(Product $product, ProductData $data): Product;

    public function delete(Product $product): bool;

    public function restore(Product $product): bool;

    public function getLowStock(): Collection;

    public function getActive(): Collection;

    public function search(string $term): Collection;

    public function updateStock(Product $product, int $quantity): Product;

    public function decrementStock(Product $product, int $quantity): Product;

    public function incrementStock(Product $product, int $quantity): Product;
}
