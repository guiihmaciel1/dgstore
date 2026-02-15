<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

use App\Domain\Supplier\DTOs\QuotationData;
use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Repositories\QuotationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QuotationService
{
    public function __construct(
        private readonly QuotationRepositoryInterface $repository
    ) {}

    public function find(string $id): ?Quotation
    {
        return $this->repository->find($id);
    }

    public function list(
        int $perPage = 15,
        ?string $supplierId = null,
        ?string $productId = null,
        ?string $productName = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): LengthAwarePaginator {
        return $this->repository->paginate(
            $perPage,
            $supplierId,
            $productId,
            $productName,
            $startDate,
            $endDate
        );
    }

    public function create(QuotationData $data): Quotation
    {
        return $this->repository->create($data);
    }

    public function createMany(array $quotations): Collection
    {
        return $this->repository->createMany($quotations);
    }

    public function update(Quotation $quotation, QuotationData $data): Quotation
    {
        return $this->repository->update($quotation, $data);
    }

    public function delete(Quotation $quotation): bool
    {
        return $this->repository->delete($quotation);
    }

    public function getForSupplier(string $supplierId, int $limit = 10): Collection
    {
        return $this->repository->getForSupplier($supplierId, $limit);
    }

    public function getLatestPricesForProduct(string $productName): Collection
    {
        return $this->repository->getLatestPricesForProduct($productName);
    }

    public function getTodayQuotations(): Collection
    {
        return $this->repository->getTodayQuotations();
    }

    public function getUniqueProductNames(): Collection
    {
        return $this->repository->getUniqueProductNames();
    }

    public function getPriceComparison(?string $productName = null, ?string $supplierId = null): Collection
    {
        return $this->repository->getPriceComparison($productName, $supplierId);
    }

    /**
     * Retorna as cotações mais recentes por fornecedor para um produto,
     * ordenadas pelo custo total (preço + frete) ASC.
     * A primeira da collection é a melhor cotação.
     */
    public function getBestQuotationsForProduct(string $productName): Collection
    {
        return $this->repository->getBestQuotationsForProduct($productName);
    }

    /**
     * Retorna mapa de product_name => melhor cotação para múltiplos nomes.
     * Usado para exibir a melhor cotação na listagem de reservas.
     *
     * @param  array<string>  $productNames
     * @return Collection<string, Quotation|null>
     */
    public function getBestQuotationsForProducts(array $productNames): Collection
    {
        $result = collect();

        foreach ($productNames as $name) {
            if (empty($name)) {
                continue;
            }

            $quotations = $this->getBestQuotationsForProduct($name);
            $result->put($name, $quotations->first());
        }

        return $result;
    }

    /**
     * Retorna estatísticas das cotações
     */
    public function getStatistics(): array
    {
        $today = $this->getTodayQuotations();
        $productNames = $this->getUniqueProductNames();

        return [
            'today_count' => $today->count(),
            'total_products' => $productNames->count(),
            'today_total' => $today->sum('unit_price'),
        ];
    }
}
