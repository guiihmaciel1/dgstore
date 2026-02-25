<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

readonly class CardFeeCalculationResult
{
    public function __construct(
        public string $paymentType,
        public int $installments,
        public float $mdrRate,
        public float $netAmount,
        public float $grossAmount,
        public float $feeAmount,
        public float $installmentValue,
    ) {}

    public function toArray(): array
    {
        return [
            'payment_type' => $this->paymentType,
            'installments' => $this->installments,
            'mdr_rate' => $this->mdrRate,
            'net_amount' => $this->netAmount,
            'gross_amount' => $this->grossAmount,
            'fee_amount' => $this->feeAmount,
            'installment_value' => $this->installmentValue,
        ];
    }

    public function getLabel(): string
    {
        if ($this->paymentType === 'debit') {
            return 'Débito';
        }

        return $this->installments === 1 
            ? 'Crédito 1x' 
            : "Crédito {$this->installments}x";
    }
}
