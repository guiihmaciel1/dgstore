<?php

declare(strict_types=1);

namespace App\Domain\Customer\DTOs;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $instagram = null,
        public ?string $cpf = null,
        public ?string $address = null,
        public ?string $notes = null,
        public ?string $birthDate = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? throw new \InvalidArgumentException('Campo "name" é obrigatório em CustomerData.'),
            phone: preg_replace('/\D/', '', $data['phone'] ?? throw new \InvalidArgumentException('Campo "phone" é obrigatório em CustomerData.')),
            instagram: $data['instagram'] ?? null,
            cpf: isset($data['cpf']) ? preg_replace('/\D/', '', $data['cpf']) : null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
            birthDate: $data['birth_date'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'instagram' => $this->instagram,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'address' => $this->address,
            'notes' => $this->notes,
            'birth_date' => $this->birthDate,
        ];
    }
}
