<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeSalePaymentStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Partial   = 'partial';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pendente',
            self::Paid      => 'Pago',
            self::Partial   => 'Parcial',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending   => 'yellow',
            self::Paid      => 'green',
            self::Partial   => 'blue',
            self::Cancelled => 'red',
        };
    }
}
