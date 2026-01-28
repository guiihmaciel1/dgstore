<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Services;

use App\Domain\Supplier\DTOs\SupplierData;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepositoryInterface $repository
    ) {}

    public function find(string $id): ?Supplier
    {
        return $this->repository->find($id);
    }

    public function findByCnpj(string $cnpj): ?Supplier
    {
        return $this->repository->findByCnpj($cnpj);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function active(): Collection
    {
        return $this->repository->active();
    }

    public function list(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $search, $active);
    }

    public function create(SupplierData $data): Supplier
    {
        return $this->repository->create($data);
    }

    public function update(Supplier $supplier, SupplierData $data): Supplier
    {
        return $this->repository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): bool
    {
        return $this->repository->delete($supplier);
    }

    public function restore(Supplier $supplier): bool
    {
        return $this->repository->restore($supplier);
    }

    public function search(string $term): Collection
    {
        return $this->repository->search($term);
    }

    public function getWithQuotations(string $id): ?Supplier
    {
        return $this->repository->getWithQuotations($id);
    }

    public function formatCnpj(?string $cnpj): ?string
    {
        if (!$cnpj) {
            return null;
        }

        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }

        return preg_replace(
            '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
            '$1.$2.$3/$4-$5',
            $cnpj
        );
    }

    public function validateCnpj(string $cnpj): bool
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação dos dígitos verificadores
        $multipliers1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $multipliers2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        // Primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $multipliers1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cnpj[12] !== $digit1) {
            return false;
        }

        // Segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $multipliers2[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $cnpj[13] === $digit2;
    }
}
