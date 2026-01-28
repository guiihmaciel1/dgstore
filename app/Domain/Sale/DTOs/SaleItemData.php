<?php

declare(strict_types=1);

namespace App\Domain\Sale\DTOs;

readonly class SaleItemData
{
    public function __construct(
        public string $productId,
        public int $quantity,
        public float $unitPrice,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: (int) $data['quantity'],
            unitPrice: (float) $data['unit_price'],
        );
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'subtotal' => $this->subtotal(),
        ];
    }
}
