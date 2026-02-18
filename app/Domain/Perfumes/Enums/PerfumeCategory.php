<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeCategory: string
{
    case Masculino = 'masculino';
    case Feminino  = 'feminino';
    case Unissex   = 'unissex';

    public function label(): string
    {
        return match ($this) {
            self::Masculino => 'Masculino',
            self::Feminino  => 'Feminino',
            self::Unissex   => 'Unissex',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Masculino => 'blue',
            self::Feminino  => 'pink',
            self::Unissex   => 'purple',
        };
    }
}
