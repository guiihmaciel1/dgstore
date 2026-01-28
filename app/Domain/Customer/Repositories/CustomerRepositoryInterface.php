<?php

declare(strict_types=1);

namespace App\Domain\Customer\Repositories;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CustomerRepositoryInterface
{
    public function find(string $id): ?Customer;

    public function findByPhone(string $phone): ?Customer;

    public function findByEmail(string $email): ?Customer;

    public function findByCpf(string $cpf): ?Customer;

    public function all(): Collection;

    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function create(CustomerData $data): Customer;

    public function update(Customer $customer, CustomerData $data): Customer;

    public function delete(Customer $customer): bool;

    public function restore(Customer $customer): bool;

    public function search(string $term): Collection;

    public function getWithPurchaseHistory(string $id): ?Customer;
}
