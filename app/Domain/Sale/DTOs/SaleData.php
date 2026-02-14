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
     * @param array<TradeInData> $tradeIns
     */
    public function __construct(
        public string $userId,
        public PaymentMethod $paymentMethod,
        public array $items,
        public ?string $customerId = null,
        public float $discount = 0,
        public float $tradeInValue = 0,
        public float $cashPayment = 0,
        public float $cardPayment = 0,
        public ?string $cashPaymentMethod = null,
        public array $tradeIns = [],
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

        // Parse trade-ins (suporta múltiplos aparelhos)
        $tradeIns = [];
        if (!empty($data['trade_ins']) && is_array($data['trade_ins'])) {
            foreach ($data['trade_ins'] as $tradeInItem) {
                if (!empty($tradeInItem['device_name'])) {
                    $tradeIns[] = TradeInData::fromArray($tradeInItem);
                }
            }
        }

        return new self(
            userId: $data['user_id'],
            paymentMethod: $data['payment_method'] instanceof PaymentMethod
                ? $data['payment_method']
                : PaymentMethod::from($data['payment_method']),
            items: $items,
            customerId: !empty($data['customer_id']) ? $data['customer_id'] : null,
            discount: (float) ($data['discount'] ?? 0),
            tradeInValue: (float) ($data['trade_in_value'] ?? 0),
            cashPayment: (float) ($data['cash_payment'] ?? 0),
            cardPayment: (float) ($data['card_payment'] ?? 0),
            cashPaymentMethod: !empty($data['cash_payment_method']) ? $data['cash_payment_method'] : null,
            tradeIns: $tradeIns,
            paymentStatus: isset($data['payment_status'])
                ? ($data['payment_status'] instanceof PaymentStatus
                    ? $data['payment_status']
                    : PaymentStatus::from($data['payment_status']))
                : PaymentStatus::Pending,
            installments: (int) ($data['installments'] ?? 1),
            notes: !empty($data['notes']) ? $data['notes'] : null,
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

    public function hasTradeIn(): bool
    {
        return count($this->tradeIns) > 0 && $this->tradeInValue > 0;
    }

    public function hasMixedPayment(): bool
    {
        $methods = 0;
        if ($this->tradeInValue > 0) $methods++;
        if ($this->cashPayment > 0) $methods++;
        if ($this->cardPayment > 0) $methods++;
        return $methods > 1;
    }
}
