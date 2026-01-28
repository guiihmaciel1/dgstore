<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Supplier\DTOs\SupplierData;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function find(string $id): ?Supplier
    {
        return Supplier::find($id);
    }

    public function findByCnpj(string $cnpj): ?Supplier
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        return Supplier::where('cnpj', $cnpj)->first();
    }

    public function all(): Collection
    {
        return Supplier::orderBy('name')->get();
    }

    public function active(): Collection
    {
        return Supplier::active()->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        $query = Supplier::query();

        if ($search) {
            $query->search($search);
        }

        if ($active !== null) {
            $query->where('active', $active);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(SupplierData $data): Supplier
    {
        return Supplier::create($data->toArray());
    }

    public function update(Supplier $supplier, SupplierData $data): Supplier
    {
        $supplier->update($data->toArray());
        return $supplier->fresh();
    }

    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }

    public function restore(Supplier $supplier): bool
    {
        return (bool) $supplier->restore();
    }

    public function search(string $term): Collection
    {
        return Supplier::active()
            ->search($term)
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function getWithQuotations(string $id): ?Supplier
    {
        return Supplier::with(['quotations' => function ($query) {
            $query->with(['product', 'user'])
                ->orderBy('quoted_at', 'desc')
                ->orderBy('created_at', 'desc');
        }])->find($id);
    }
}
