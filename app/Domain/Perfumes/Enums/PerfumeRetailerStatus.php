<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeRetailerStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Ativo',
            self::Inactive => 'Inativo',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active   => 'green',
            self::Inactive => 'red',
        };
    }
}
