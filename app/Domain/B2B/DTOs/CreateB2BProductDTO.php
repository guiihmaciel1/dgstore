<?php

declare(strict_types=1);

namespace App\Domain\B2B\DTOs;

class CreateB2BProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $model,
        public readonly ?string $storage,
        public readonly ?string $color,
        public readonly string $condition,
        public readonly float $costPrice,
        public readonly float $wholesalePrice,
        public readonly int $stockQuantity,
        public readonly ?string $photo,
        public readonly int $sortOrder = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            model: $data['model'] ?? null,
            storage: $data['storage'] ?? null,
            color: $data['color'] ?? null,
            condition: $data['condition'],
            costPrice: (float) $data['cost_price'],
            wholesalePrice: (float) $data['wholesale_price'],
            stockQuantity: (int) ($data['stock_quantity'] ?? 0),
            photo: $data['photo'] ?? null,
            sortOrder: (int) ($data['sort_order'] ?? 0),
        );
    }
}
