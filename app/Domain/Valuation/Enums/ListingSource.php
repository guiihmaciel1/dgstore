<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Enums;

enum ListingSource: string
{
    case MercadoLivre = 'mercadolivre';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::MercadoLivre => 'Mercado Livre',
            self::Manual => 'Manual',
        };
    }
}
