<?php

declare(strict_types=1);

namespace App\Domain\Import\Enums;

enum ImportOrderStatus: string
{
    case Ordered = 'ordered';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Customs = 'customs';
    case Received = 'received';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Ordered => 'Pedido Realizado',
            self::Shipped => 'Enviado',
            self::InTransit => 'Em Trânsito',
            self::Customs => 'Na Alfândega',
            self::Received => 'Recebido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ordered => 'gray',
            self::Shipped => 'blue',
            self::InTransit => 'indigo',
            self::Customs => 'yellow',
            self::Received => 'green',
            self::Cancelled => 'red',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Ordered => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            self::Shipped => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
            self::InTransit => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
            self::Customs => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            self::Received => 'M5 13l4 4L19 7',
            self::Cancelled => 'M6 18L18 6M6 6l12 12',
        };
    }

    public function step(): int
    {
        return match ($this) {
            self::Ordered => 1,
            self::Shipped => 2,
            self::InTransit => 3,
            self::Customs => 4,
            self::Received => 5,
            self::Cancelled => 0,
        };
    }

    public function isActive(): bool
    {
        return !in_array($this, [self::Received, self::Cancelled]);
    }

    public function canAdvanceTo(ImportOrderStatus $nextStatus): bool
    {
        if ($this === self::Cancelled || $this === self::Received) {
            return false;
        }

        return $nextStatus->step() > $this->step() || $nextStatus === self::Cancelled;
    }

    public static function activeStatuses(): array
    {
        return [self::Ordered, self::Shipped, self::InTransit, self::Customs];
    }
}
