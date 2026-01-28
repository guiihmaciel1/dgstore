<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email = null,
        public ?string $cpf = null,
        public ?string $address = null,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            phone: preg_replace('/\D/', '', $data['phone']),
            email: $data['email'] ?? null,
            cpf: isset($data['cpf']) ? preg_replace('/\D/', '', $data['cpf']) : null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'address' => $this->address,
            'notes' => $this->notes,
        ];
    }
}
