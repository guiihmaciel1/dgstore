<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Enums;

enum ListingSource: string
{
    case Olx = 'olx';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Olx => 'OLX',
            self::Manual => 'Manual',
        };
    }
}
