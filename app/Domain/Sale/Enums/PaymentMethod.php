<?php

declare(strict_types=1);

namespace App\Domain\Sale\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case Pix = 'pix';
    case BankTransfer = 'bank_transfer';
    case Installment = 'installment';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Dinheiro',
            self::CreditCard => 'Cartão de Crédito',
            self::DebitCard => 'Cartão de Débito',
            self::Pix => 'PIX',
            self::BankTransfer => 'Transferência Bancária',
            self::Installment => 'Parcelado',
        };
    }
}
