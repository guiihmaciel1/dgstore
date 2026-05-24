<?php

namespace App\Domain\Sale\Enums;

enum SaleType: string
{
    case ClienteFinal = 'cliente_final';
    case Repasse = 'repasse';

    public function label(): string
    {
        return match($this) {
            self::ClienteFinal => 'Cliente Final',
            self::Repasse => 'Repasse',
        };
    }
}
