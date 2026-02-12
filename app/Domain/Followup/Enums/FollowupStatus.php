<?php

declare(strict_types=1);

namespace App\Domain\Followup\Enums;

enum FollowupStatus: string
{
    case Pending = 'pending';
    case Done = 'done';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Done => 'Concluido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Done => 'green',
            self::Cancelled => 'gray',
        };
    }
}
