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
        public ?float $price_usd = null,
        public ?float $exchange_rate = null,
        public ?string $category = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            supplier_id: $data['supplier_id'] ?? throw new \InvalidArgumentException('Campo "supplier_id" é obrigatório em QuotationData.'),
            user_id: $data['user_id'] ?? throw new \InvalidArgumentException('Campo "user_id" é obrigatório em QuotationData.'),
            product_name: $data['product_name'] ?? throw new \InvalidArgumentException('Campo "product_name" é obrigatório em QuotationData.'),
            unit_price: (float) ($data['unit_price'] ?? throw new \InvalidArgumentException('Campo "unit_price" é obrigatório em QuotationData.')),
            quoted_at: $data['quoted_at'] ?? throw new \InvalidArgumentException('Campo "quoted_at" é obrigatório em QuotationData.'),
            product_id: $data['product_id'] ?? null,
            quantity: (float) ($data['quantity'] ?? 1),
            unit: $data['unit'] ?? 'un',
            notes: $data['notes'] ?? null,
            price_usd: isset($data['price_usd']) ? (float) $data['price_usd'] : null,
            exchange_rate: isset($data['exchange_rate']) ? (float) $data['exchange_rate'] : null,
            category: $data['category'] ?? null,
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
            'price_usd' => $this->price_usd,
            'exchange_rate' => $this->exchange_rate,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'quoted_at' => $this->quoted_at,
            'notes' => $this->notes,
            'category' => $this->category,
        ];
    }
}
