<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Enums;

enum PerfumeOrderStatus: string
{
    case Received   = 'received';
    case Separating = 'separating';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Received   => 'Recebido',
            self::Separating => 'Separando',
            self::Shipped    => 'Enviado',
            self::Delivered  => 'Entregue',
            self::Cancelled  => 'Cancelado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Received   => 'blue',
            self::Separating => 'yellow',
            self::Shipped    => 'indigo',
            self::Delivered  => 'green',
            self::Cancelled  => 'red',
        };
    }

    /** Próximos statuses possíveis */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Received   => [self::Separating, self::Cancelled],
            self::Separating => [self::Shipped, self::Cancelled],
            self::Shipped    => [self::Delivered, self::Cancelled],
            self::Delivered  => [],
            self::Cancelled  => [],
        };
    }
}
