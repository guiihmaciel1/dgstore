<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Enums;

enum ReservationStatus: string
{
    case Active = 'active';
    case Converted = 'converted';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativa',
            self::Converted => 'Convertida em Venda',
            self::Cancelled => 'Cancelada',
            self::Expired => 'Expirada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Converted => 'blue',
            self::Cancelled => 'red',
            self::Expired => 'gray',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Converted, self::Cancelled, self::Expired]);
    }

    public function canConvert(): bool
    {
        return $this === self::Active;
    }

    public function canCancel(): bool
    {
        return $this === self::Active;
    }
}
