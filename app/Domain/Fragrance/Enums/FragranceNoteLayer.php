<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Enums;

enum FragranceNoteLayer: string
{
    case Top   = 'top';
    case Heart = 'heart';
    case Base  = 'base';

    public function label(): string
    {
        return match ($this) {
            self::Top   => 'Notas de Topo',
            self::Heart => 'Notas de Coração',
            self::Base  => 'Notas de Base',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Top   => '△',
            self::Heart => '♡',
            self::Base  => '▽',
        };
    }
}
