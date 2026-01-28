<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function find(string $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByPhone(string $phone): ?Customer
    {
        $phone = preg_replace('/\D/', '', $phone);
        return Customer::where('phone', $phone)->first();
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function findByCpf(string $cpf): ?Customer
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        return Customer::where('cpf', $cpf)->first();
    }

    public function all(): Collection
    {
        return Customer::orderBy('name')->get();
    }

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = Customer::query();

        if ($search) {
            $searchClean = preg_replace('/\D/', '', $search);
            
            $query->where(function ($q) use ($search, $searchClean) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                
                if ($searchClean) {
                    $q->orWhere('phone', 'like', "%{$searchClean}%")
                        ->orWhere('cpf', 'like', "%{$searchClean}%");
                }
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(CustomerData $data): Customer
    {
        return Customer::create($data->toArray());
    }

    public function update(Customer $customer, CustomerData $data): Customer
    {
        $customer->update($data->toArray());
        return $customer->fresh();
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    public function restore(Customer $customer): bool
    {
        return (bool) $customer->restore();
    }

    public function search(string $term): Collection
    {
        $termClean = preg_replace('/\D/', '', $term);

        return Customer::where(function ($query) use ($term, $termClean) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
            
            if ($termClean) {
                $query->orWhere('phone', 'like', "%{$termClean}%")
                    ->orWhere('cpf', 'like', "%{$termClean}%");
            }
        })
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function getWithPurchaseHistory(string $id): ?Customer
    {
        return Customer::with(['sales' => function ($query) {
            $query->with('items.product')
                ->orderBy('sold_at', 'desc');
        }])->find($id);
    }
}
