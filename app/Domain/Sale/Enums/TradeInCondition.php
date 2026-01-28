<?php

declare(strict_types=1);

namespace App\Domain\Sale\Enums;

enum TradeInCondition: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case Fair = 'fair';
    case Poor = 'poor';

    public function label(): string
    {
        return match ($this) {
            self::Excellent => 'Excelente',
            self::Good => 'Bom',
            self::Fair => 'Regular',
            self::Poor => 'Ruim',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Excellent => 'green',
            self::Good => 'blue',
            self::Fair => 'yellow',
            self::Poor => 'red',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Excellent => 'Sem arranhões, funcionando perfeitamente',
            self::Good => 'Pequenos sinais de uso, funcional',
            self::Fair => 'Arranhões visíveis, funcionando',
            self::Poor => 'Danos visíveis ou problemas funcionais',
        };
    }
}
