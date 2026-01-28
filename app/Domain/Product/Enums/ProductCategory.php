<?php

declare(strict_types=1);

namespace App\Domain\Product\Enums;

enum ProductCategory: string
{
    case Iphone = 'iphone';
    case Accessory = 'accessory';
    case Service = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Iphone => 'iPhone',
            self::Accessory => 'Acessório',
            self::Service => 'Serviço',
        };
    }
}
