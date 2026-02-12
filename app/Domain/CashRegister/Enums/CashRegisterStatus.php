<?php

declare(strict_types=1);

namespace App\Domain\CashRegister\Enums;

enum CashRegisterStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Aberto',
            self::Closed => 'Fechado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'green',
            self::Closed => 'gray',
        };
    }
}
