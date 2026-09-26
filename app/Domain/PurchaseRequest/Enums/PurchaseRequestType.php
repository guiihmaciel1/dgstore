<?php

declare(strict_types=1);

namespace App\Domain\PurchaseRequest\Enums;

enum PurchaseRequestType: string
{
    case DirectPurchase = 'direct_purchase';
    case Upgrade = 'upgrade';

    public function label(): string
    {
        return match ($this) {
            self::DirectPurchase => 'Compra Direta',
            self::Upgrade => 'Upgrade',
        };
    }
}
