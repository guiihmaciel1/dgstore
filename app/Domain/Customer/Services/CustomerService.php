<?php

declare(strict_types=1);

namespace App\Domain\Customer\Services;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $repository
    ) {}

    public function find(string $id): ?Customer
    {
        return $this->repository->find($id);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->repository->findByPhone($phone);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->repository->findByEmail($email);
    }

    public function findByCpf(string $cpf): ?Customer
    {
        return $this->repository->findByCpf($cpf);
    }

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search);
    }

    public function create(CustomerData $data): Customer
    {
        return $this->repository->create($data);
    }

    public function update(Customer $customer, CustomerData $data): Customer
    {
        return $this->repository->update($customer, $data);
    }

    public function delete(Customer $customer): bool
    {
        return $this->repository->delete($customer);
    }

    public function restore(Customer $customer): bool
    {
        return $this->repository->restore($customer);
    }

    public function search(string $term): Collection
    {
        return $this->repository->search($term);
    }

    public function getWithPurchaseHistory(string $id): ?Customer
    {
        return $this->repository->getWithPurchaseHistory($id);
    }

    public function formatCpf(?string $cpf): ?string
    {
        if (!$cpf) {
            return null;
        }

        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
    }

    public function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
