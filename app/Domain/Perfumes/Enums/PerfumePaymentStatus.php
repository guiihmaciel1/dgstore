<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumePaymentStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Paid    = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Partial => 'Parcial',
            self::Paid    => 'Pago',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'red',
            self::Partial => 'yellow',
            self::Paid    => 'green',
        };
    }
}
