<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeReservationStatus: string
{
    case Active    = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired   = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Ativa',
            self::Completed => 'Concluída',
            self::Cancelled => 'Cancelada',
            self::Expired   => 'Expirada',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active    => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
            self::Expired   => 'gray',
        };
    }
}
