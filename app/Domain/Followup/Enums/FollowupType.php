<?php

declare(strict_types=1);

namespace App\Domain\Followup\Enums;

enum FollowupType: string
{
    case FollowUp = 'follow_up';
    case WarrantyCheck = 'warranty_check';
    case ProductArrived = 'product_arrived';
    case Reservation = 'reservation';
    case Callback = 'callback';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FollowUp => 'Follow-up',
            self::WarrantyCheck => 'Verificar Garantia',
            self::ProductArrived => 'Produto Chegou',
            self::Reservation => 'Reserva',
            self::Callback => 'Retorno',
            self::Other => 'Outro',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FollowUp => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            self::WarrantyCheck => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            self::ProductArrived => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            self::Reservation => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z',
            self::Callback => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            self::Other => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        };
    }
}
