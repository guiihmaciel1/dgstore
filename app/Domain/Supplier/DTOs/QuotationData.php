<?php

declare(strict_types=1);

namespace App\Domain\Supplier\DTOs;

readonly class QuotationData
{
    public function __construct(
        public string $supplier_id,
        public string $user_id,
        public string $product_name,
        public float $unit_price,
        public string $quoted_at,
        public ?string $product_id = null,
        public float $quantity = 1,
        public string $unit = 'un',
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            supplier_id: $data['supplier_id'],
            user_id: $data['user_id'],
            product_name: $data['product_name'],
            unit_price: (float) $data['unit_price'],
            quoted_at: $data['quoted_at'],
            product_id: $data['product_id'] ?? null,
            quantity: (float) ($data['quantity'] ?? 1),
            unit: $data['unit'] ?? 'un',
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'supplier_id' => $this->supplier_id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'product_name' => $this->product_name,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'quoted_at' => $this->quoted_at,
            'notes' => $this->notes,
        ];
    }
}
