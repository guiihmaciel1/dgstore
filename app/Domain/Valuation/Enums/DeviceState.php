<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Enums;

enum DeviceState: string
{
    case Original = 'original';
    case Repaired = 'repaired';

    public function label(): string
    {
        return match ($this) {
            self::Original => 'Original (nunca aberto)',
            self::Repaired => 'Já foi aberto/trocou peça',
        };
    }

    /**
     * Modificador de preço baseado no estado do aparelho.
     */
    public function priceModifier(): float
    {
        return match ($this) {
            self::Original => 0.0,
            self::Repaired => -0.10,
        };
    }
}
