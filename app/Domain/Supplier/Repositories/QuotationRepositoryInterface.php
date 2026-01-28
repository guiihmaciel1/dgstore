<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Repositories;

use App\Domain\Supplier\DTOs\QuotationData;
use App\Domain\Supplier\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface QuotationRepositoryInterface
{
    public function find(string $id): ?Quotation;

    public function all(): Collection;

    public function paginate(
        int $perPage = 15,
        ?string $supplierId = null,
        ?string $productId = null,
        ?string $productName = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): LengthAwarePaginator;

    public function create(QuotationData $data): Quotation;

    public function createMany(array $quotations): Collection;

    public function update(Quotation $quotation, QuotationData $data): Quotation;

    public function delete(Quotation $quotation): bool;

    public function getForSupplier(string $supplierId, int $limit = 10): Collection;

    public function getLatestPricesForProduct(string $productName): Collection;

    public function getTodayQuotations(): Collection;

    public function getUniqueProductNames(): Collection;

    public function getPriceComparison(?string $productName = null): Collection;
}
