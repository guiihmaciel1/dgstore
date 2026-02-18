<?php

declare(strict_types=1);

namespace App\Domain\B2B\DTOs;

class CreateRetailerDTO
{
    public function __construct(
        public readonly string $storeName,
        public readonly string $ownerName,
        public readonly string $document,
        public readonly string $whatsapp,
        public readonly string $city,
        public readonly string $state,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            storeName: $data['store_name'],
            ownerName: $data['owner_name'],
            document: $data['document'],
            whatsapp: $data['whatsapp'],
            city: $data['city'],
            state: $data['state'],
            email: $data['email'],
            password: $data['password'],
        );
    }
}
