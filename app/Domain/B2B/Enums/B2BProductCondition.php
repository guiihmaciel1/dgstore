<?php

declare(strict_types=1);

namespace App\Domain\B2B\Enums;

enum B2BProductCondition: string
{
    case Sealed = 'sealed';
    case SemiNew = 'semi_new';

    public function label(): string
    {
        return match ($this) {
            self::Sealed => 'Lacrado',
            self::SemiNew => 'Semi-novo',
        };
    }
}
