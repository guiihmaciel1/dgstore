<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Enums;

enum BatteryHealth: string
{
    case Excellent = 'excellent'; // >= 90%
    case Good = 'good';          // 80-89%
    case Fair = 'fair';          // < 80%

    public function label(): string
    {
        return match ($this) {
            self::Excellent => 'Excelente (≥ 90%)',
            self::Good => 'Bom (80-89%)',
            self::Fair => 'Regular (< 80%)',
        };
    }

    /**
     * Modificador de preço baseado na saúde da bateria.
     */
    public function priceModifier(): float
    {
        return match ($this) {
            self::Excellent => 0.0,
            self::Good => -0.05,
            self::Fair => -0.15,
        };
    }

    /**
     * Determina a faixa a partir do percentual informado.
     */
    public static function fromPercentage(int $percentage): self
    {
        return match (true) {
            $percentage >= 90 => self::Excellent,
            $percentage >= 80 => self::Good,
            default => self::Fair,
        };
    }
}
