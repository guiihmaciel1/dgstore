<?php

declare(strict_types=1);

namespace App\Domain\Stock\Enums;

enum StockMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Adjustment = 'adjustment';
    case Return = 'return';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Entrada',
            self::Out => 'Saída',
            self::Adjustment => 'Ajuste',
            self::Return => 'Devolução',
        };
    }

    public function isAddition(): bool
    {
        return in_array($this, [self::In, self::Return]);
    }
}
