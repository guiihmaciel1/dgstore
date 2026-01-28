<?php

declare(strict_types=1);

namespace App\Domain\Product\Enums;

enum ProductCondition: string
{
    case New = 'new';
    case Used = 'used';
    case Refurbished = 'refurbished';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Novo',
            self::Used => 'Usado',
            self::Refurbished => 'Recondicionado',
        };
    }
}
