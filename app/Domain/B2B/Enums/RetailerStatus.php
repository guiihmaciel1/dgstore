<?php

declare(strict_types=1);

namespace App\Domain\B2B\Enums;

enum RetailerStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Approved => 'Aprovado',
            self::Blocked => 'Bloqueado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Approved => 'green',
            self::Blocked => 'red',
        };
    }
}
