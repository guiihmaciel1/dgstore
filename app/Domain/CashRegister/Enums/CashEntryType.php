<?php

declare(strict_types=1);

namespace App\Domain\CashRegister\Enums;

enum CashEntryType: string
{
    case Sale = 'sale';
    case Withdrawal = 'withdrawal';   // Sangria
    case Supply = 'supply';           // Suprimento
    case TradeIn = 'trade_in';        // Pagamento trade-in
    case Expense = 'expense';         // Despesa avulsa

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Venda',
            self::Withdrawal => 'Sangria',
            self::Supply => 'Suprimento',
            self::TradeIn => 'Trade-in',
            self::Expense => 'Despesa',
        };
    }

    public function isInflow(): bool
    {
        return in_array($this, [self::Sale, self::Supply]);
    }

    public function color(): string
    {
        return match ($this) {
            self::Sale => 'green',
            self::Supply => 'blue',
            self::Withdrawal => 'red',
            self::TradeIn => 'purple',
            self::Expense => 'orange',
        };
    }
}
