<?php

declare(strict_types=1);

namespace App\Domain\Finance\Enums;

enum TransactionType: string
{
    case Income = 'income';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Income => 'Receita',
            self::Expense => 'Despesa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Income => '#16a34a',
            self::Expense => '#dc2626',
        };
    }
}
