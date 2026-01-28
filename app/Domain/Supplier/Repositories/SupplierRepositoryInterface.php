<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Repositories;

use App\Domain\Supplier\DTOs\SupplierData;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SupplierRepositoryInterface
{
    public function find(string $id): ?Supplier;

    public function findByCnpj(string $cnpj): ?Supplier;

    public function all(): Collection;

    public function active(): Collection;

    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator;

    public function create(SupplierData $data): Supplier;

    public function update(Supplier $supplier, SupplierData $data): Supplier;

    public function delete(Supplier $supplier): bool;

    public function restore(Supplier $supplier): bool;

    public function search(string $term): Collection;

    public function getWithQuotations(string $id): ?Supplier;
}
