<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Supplier\DTOs\QuotationData;
use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Repositories\QuotationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentQuotationRepository implements QuotationRepositoryInterface
{
    public function find(string $id): ?Quotation
    {
        return Quotation::with(['supplier', 'product', 'user'])->find($id);
    }

    public function all(): Collection
    {
        return Quotation::with(['supplier', 'product', 'user'])
            ->orderBy('quoted_at', 'desc')
            ->get();
    }

    public function paginate(
        int $perPage = 15,
        ?string $supplierId = null,
        ?string $productId = null,
        ?string $productName = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): LengthAwarePaginator {
        $query = Quotation::with(['supplier', 'product', 'user']);

        if ($supplierId) {
            $query->forSupplier($supplierId);
        }

        if ($productId) {
            $query->forProduct($productId);
        }

        if ($productName) {
            $query->forProductName($productName);
        }

        if ($startDate || $endDate) {
            $query->betweenDates($startDate, $endDate);
        }

        return $query->orderBy('quoted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(QuotationData $data): Quotation
    {
        return Quotation::create($data->toArray());
    }

    public function createMany(array $quotations): Collection
    {
        $created = collect();

        DB::transaction(function () use ($quotations, &$created) {
            foreach ($quotations as $quotationData) {
                if ($quotationData instanceof QuotationData) {
                    $created->push(Quotation::create($quotationData->toArray()));
                } else {
                    $created->push(Quotation::create($quotationData));
                }
            }
        });

        return $created;
    }

    public function update(Quotation $quotation, QuotationData $data): Quotation
    {
        $quotation->update($data->toArray());
        return $quotation->fresh(['supplier', 'product', 'user']);
    }

    public function delete(Quotation $quotation): bool
    {
        return (bool) $quotation->delete();
    }

    public function getForSupplier(string $supplierId, int $limit = 10): Collection
    {
        return Quotation::with(['product', 'user'])
            ->forSupplier($supplierId)
            ->orderBy('quoted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getLatestPricesForProduct(string $productName): Collection
    {
        return Quotation::with('supplier')
            ->latestPricePerSupplier($productName)
            ->orderBy('unit_price')
            ->get();
    }

    public function getTodayQuotations(): Collection
    {
        return Quotation::with(['supplier', 'product', 'user'])
            ->today()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUniqueProductNames(): Collection
    {
        return Quotation::select('product_name')
            ->distinct()
            ->orderBy('product_name')
            ->pluck('product_name');
    }

    public function getBestQuotationsForProduct(string $productName): Collection
    {
        return Quotation::with('supplier')
            ->latestPricePerSupplier($productName)
            ->get()
            ->map(function (Quotation $q) {
                // PY = 4%, BR = 0%, null = 4% (padrão PY, mesmo comportamento da view de cotações)
                $origin = $q->supplier?->origin;
                $freightPercent = ($origin === null || $origin->value === 'py') ? 0.04 : 0.0;

                $unitPrice = (float) $q->unit_price;
                $freightCost = round($unitPrice * $freightPercent, 2);
                $totalCost = round($unitPrice + $freightCost, 2);

                $q->setAttribute('freight_percent', $freightPercent);
                $q->setAttribute('freight_cost', $freightCost);
                $q->setAttribute('total_cost', $totalCost);

                return $q;
            })
            ->sortBy('total_cost')
            ->values();
    }

    public function getPriceComparison(?string $productName = null, ?string $supplierId = null): Collection
    {
        $query = Quotation::with('supplier')
            ->select('quotations.*')
            ->whereIn('quotations.id', function ($subquery) use ($supplierId) {
                $subquery->select(DB::raw('MAX(id)'))
                    ->from('quotations');

                if ($supplierId) {
                    $subquery->where('supplier_id', $supplierId);
                }

                $subquery->groupBy('supplier_id', 'product_name');
            });

        if ($supplierId) {
            $query->where('quotations.supplier_id', $supplierId);
        }

        if ($productName) {
            $query->where('product_name', 'like', "%{$productName}%");
        }

        $quotations = $query->get();

        // Agrupar por produto e ordenar por preço
        return $quotations->groupBy('product_name')
            ->map(function ($items) {
                return $items->sortBy('unit_price')->values();
            })
            ->sortKeys();
    }
}
