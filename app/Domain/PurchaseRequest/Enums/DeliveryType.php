<?php

declare(strict_types=1);

namespace App\Domain\PurchaseRequest\Enums;

enum DeliveryType: string
{
    case Pickup = 'pickup';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::Pickup => 'Retira na Loja',
            self::Delivery => 'Entrega',
        };
    }
}
