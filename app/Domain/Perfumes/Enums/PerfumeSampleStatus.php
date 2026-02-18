<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeSampleStatus: string
{
    case Delivered    = 'delivered';
    case WithRetailer = 'with_retailer';
    case Returned     = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Delivered    => 'Entregue',
            self::WithRetailer => 'Com Lojista',
            self::Returned     => 'Devolvido',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Delivered    => 'blue',
            self::WithRetailer => 'yellow',
            self::Returned     => 'green',
        };
    }
}
