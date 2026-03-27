<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Enums;

enum ConsignmentStatus: string
{
    case Available = 'available';
    case Sold = 'sold';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponível',
            self::Sold => 'Vendido',
            self::Returned => 'Devolvido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Sold => 'blue',
            self::Returned => 'gray',
        };
    }
}
