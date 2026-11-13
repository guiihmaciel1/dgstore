<?php

declare(strict_types=1);

namespace App\Domain\Finance\Enums;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Paid => 'Pago',
            self::Overdue => 'Vencido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => '#fbbf24',
            self::Paid => '#34d399',
            self::Overdue => '#f87171',
            self::Cancelled => '#a4a4a4',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::Pending => 'rgba(245,158,11,0.15)',
            self::Paid => 'rgba(16,185,129,0.15)',
            self::Overdue => 'rgba(239,68,68,0.15)',
            self::Cancelled => 'rgba(255,255,255,0.06)',
        };
    }
}
