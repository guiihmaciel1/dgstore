<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeSalePaymentMethod: string
{
    case Cash  = 'cash';
    case Card  = 'card';
    case Pix   = 'pix';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Cash  => 'Dinheiro',
            self::Card  => 'Cartão',
            self::Pix   => 'PIX',
            self::Mixed => 'Misto',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash  => 'cash',
            self::Card  => 'credit-card',
            self::Pix   => 'smartphone',
            self::Mixed => 'layers',
        };
    }
}
