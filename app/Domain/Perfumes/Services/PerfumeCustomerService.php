<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Services;

use App\Domain\Perfumes\Models\PerfumeCustomer;

class PerfumeCustomerService
{
    public function create(array $data): PerfumeCustomer
    {
        return PerfumeCustomer::create([
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'cpf'        => $data['cpf'] ?? null,
            'email'      => $data['email'] ?? null,
            'address'    => $data['address'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'notes'      => $data['notes'] ?? null,
        ]);
    }

    public function update(PerfumeCustomer $customer, array $data): void
    {
        $customer->update([
            'name'       => $data['name'] ?? $customer->name,
            'phone'      => $data['phone'] ?? $customer->phone,
            'cpf'        => $data['cpf'] ?? $customer->cpf,
            'email'      => $data['email'] ?? $customer->email,
            'address'    => $data['address'] ?? $customer->address,
            'birth_date' => $data['birth_date'] ?? $customer->birth_date,
            'notes'      => $data['notes'] ?? $customer->notes,
        ]);
    }

    public function delete(PerfumeCustomer $customer): void
    {
        $customer->delete();
    }
}
