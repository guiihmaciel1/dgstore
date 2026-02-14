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
            self::Pending => '#d97706',
            self::Paid => '#16a34a',
            self::Overdue => '#dc2626',
            self::Cancelled => '#6b7280',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::Pending => '#fef3c7',
            self::Paid => '#dcfce7',
            self::Overdue => '#fef2f2',
            self::Cancelled => '#f3f4f6',
        };
    }
}
