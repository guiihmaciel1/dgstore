<?php

declare(strict_types=1);

namespace App\Domain\PreSale\Enums;

enum PreSaleStatus: string
{
    case Pending = 'pending';
    case Ready = 'ready';
    case Converted = 'converted';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Ready => 'Pronta',
            self::Converted => 'Efetivada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'background: rgba(202,138,4,0.12); color: #fbbf24;',
            self::Ready => 'background: rgba(59,130,246,0.12); color: #60a5fa;',
            self::Converted => 'background: rgba(22,163,106,0.12); color: #4ade80;',
            self::Cancelled => 'background: rgba(220,38,38,0.12); color: #f87171;',
        };
    }
}
