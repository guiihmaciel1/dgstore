<?php

declare(strict_types=1);

namespace App\Domain\PurchaseRequest\Enums;

enum PurchaseRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Purchased = 'purchased';
    case Delivered = 'delivered';
    case Converted = 'converted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Approved => 'Aprovada',
            self::Purchased => 'Comprada',
            self::Delivered => 'Entregue',
            self::Converted => 'Efetivada',
            self::Rejected => 'Rejeitada',
            self::Cancelled => 'Cancelada',
            self::Expired => 'Expirada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'background: rgba(202,138,4,0.12); color: #fbbf24;',
            self::Approved => 'background: rgba(59,130,246,0.12); color: #60a5fa;',
            self::Purchased => 'background: rgba(124,58,237,0.12); color: #a78bfa;',
            self::Delivered => 'background: rgba(6,182,212,0.12); color: #22d3ee;',
            self::Converted => 'background: rgba(22,163,106,0.12); color: #4ade80;',
            self::Rejected => 'background: rgba(220,38,38,0.12); color: #f87171;',
            self::Cancelled => 'background: rgba(220,38,38,0.12); color: #f87171;',
            self::Expired => 'background: rgba(107,114,128,0.12); color: #9ca3af;',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Approved, self::Purchased, self::Delivered]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Converted, self::Rejected, self::Cancelled, self::Expired]);
    }
}
