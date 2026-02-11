<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Enums;

enum AccessoryState: string
{
    case Complete = 'complete';  // Caixa + cabo
    case Partial = 'partial';   // Só caixa ou só cabo
    case None = 'none';         // Nenhum

    public function label(): string
    {
        return match ($this) {
            self::Complete => 'Possui caixa e cabo',
            self::Partial => 'Possui caixa ou cabo',
            self::None => 'Nenhum',
        };
    }

    /**
     * Modificador de preço baseado nos acessórios.
     */
    public function priceModifier(): float
    {
        return match ($this) {
            self::Complete => 0.03,
            self::Partial => 0.0,
            self::None => -0.03,
        };
    }
}
