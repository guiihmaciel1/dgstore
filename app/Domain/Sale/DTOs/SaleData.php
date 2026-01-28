<?php

declare(strict_types=1);

namespace App\Domain\Sale\DTOs;

use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use Carbon\Carbon;

readonly class SaleData
{
    /**
     * @param array<SaleItemData> $items
     */
    public function __construct(
        public string $userId,
        public PaymentMethod $paymentMethod,
        public array $items,
        public ?string $customerId = null,
        public float $discount = 0,
        public PaymentStatus $paymentStatus = PaymentStatus::Pending,
        public int $installments = 1,
        public ?string $notes = null,
        public ?Carbon $soldAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => SaleItemData::fromArray($item),
            $data['items'] ?? []
        );

        return new self(
            userId: $data['user_id'],
            paymentMethod: $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']),
            items: $items,
            customerId: $data['customer_id'] ?? null,
            discount: (float) ($data['discount'] ?? 0),
            paymentStatus: isset($data['payment_status'])
                ? ($data['payment_status'] instanceof PaymentStatus
                    ? $data['payment_status']
                    : PaymentStatus::from($data['payment_status']))
                : PaymentStatus::Pending,
            installments: (int) ($data['installments'] ?? 1),
            notes: $data['notes'] ?? null,
            soldAt: isset($data['sold_at']) 
                ? Carbon::parse($data['sold_at']) 
                : Carbon::now(),
        );
    }

    public function calculateSubtotal(): float
    {
        return array_reduce(
            $this->items,
            fn(float $total, SaleItemData $item) => $total + $item->subtotal(),
            0.0
        );
    }

    public function calculateTotal(): float
    {
        return $this->calculateSubtotal() - $this->discount;
    }
}
