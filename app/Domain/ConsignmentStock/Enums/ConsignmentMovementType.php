<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Enums;

enum ConsignmentMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Return = 'return';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Entrada',
            self::Out => 'Saída (Venda)',
            self::Return => 'Devolução',
        };
    }
}
