<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumePaymentMethod: string
{
    case Pix         = 'pix';
    case Consignment = 'consignment';

    public function label(): string
    {
        return match ($this) {
            self::Pix         => 'PIX',
            self::Consignment => 'Consignação',
        };
    }
}
