<?php

declare(strict_types=1);

namespace App\Domain\Supplier\DTOs;

readonly class SupplierData
{
    public function __construct(
        public string $name,
        public ?string $origin = null,
        public ?string $cnpj = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,
        public ?string $contact_person = null,
        public ?string $notes = null,
        public bool $active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            origin: $data['origin'] ?? null,
            cnpj: isset($data['cnpj']) ? preg_replace('/\D/', '', $data['cnpj']) : null,
            phone: isset($data['phone']) ? preg_replace('/\D/', '', $data['phone']) : null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            contact_person: $data['contact_person'] ?? null,
            notes: $data['notes'] ?? null,
            active: (bool) ($data['active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'origin' => $this->origin,
            'cnpj' => $this->cnpj,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'contact_person' => $this->contact_person,
            'notes' => $this->notes,
            'active' => $this->active,
        ];
    }
}
