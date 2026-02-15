<?php

declare(strict_types=1);

namespace App\Domain\Sale\DTOs;

readonly class SaleItemData
{
    public function __construct(
        public string $productId,
        public int $quantity,
        public float $unitPrice,
        public float $costPrice = 0,
        public ?string $supplierOrigin = null,
        public ?string $freightType = null,
        public float $freightValue = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: (int) $data['quantity'],
            unitPrice: (float) $data['unit_price'],
            costPrice: (float) ($data['cost_price'] ?? 0),
            supplierOrigin: $data['supplier_origin'] ?? null,
            freightType: $data['freight_type'] ?? null,
            freightValue: (float) ($data['freight_value'] ?? 0),
        );
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }

    /**
     * Calcula o valor do frete baseado no tipo
     */
    public function calculateFreightAmount(): float
    {
        if (!$this->supplierOrigin || !$this->freightType) {
            return 0;
        }

        return match ($this->freightType) {
            'percentage' => $this->costPrice * ($this->freightValue / 100),
            'fixed' => $this->freightValue,
            default => 0,
        };
    }

    /**
     * Calcula o custo total: custo + frete
     */
    public function calculateTotalCost(): float
    {
        return $this->costPrice + $this->calculateFreightAmount();
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
            'cost_price' => $this->costPrice,
            'supplier_origin' => $this->supplierOrigin,
            'freight_type' => $this->freightType,
            'freight_value' => $this->freightValue,
            'freight_amount' => $this->calculateFreightAmount(),
            'total_cost' => $this->calculateTotalCost(),
            'subtotal' => $this->subtotal(),
        ];
    }
}
