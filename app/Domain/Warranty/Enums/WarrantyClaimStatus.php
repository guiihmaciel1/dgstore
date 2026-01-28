<?php

declare(strict_types=1);

namespace App\Domain\Warranty\Enums;

enum WarrantyClaimStatus: string
{
    case Opened = 'opened';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Denied = 'denied';

    public function label(): string
    {
        return match ($this) {
            self::Opened => 'Aberto',
            self::InProgress => 'Em Andamento',
            self::Resolved => 'Resolvido',
            self::Denied => 'Negado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Opened => 'yellow',
            self::InProgress => 'blue',
            self::Resolved => 'green',
            self::Denied => 'red',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Opened, self::InProgress]);
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::Resolved, self::Denied]);
    }
}
