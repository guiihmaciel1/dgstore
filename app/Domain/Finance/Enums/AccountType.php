<?php

declare(strict_types=1);

namespace App\Domain\Finance\Enums;

enum AccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case DigitalWallet = 'digital_wallet';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Dinheiro',
            self::Bank => 'Banco',
            self::DigitalWallet => 'Carteira Digital',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            self::Bank => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            self::DigitalWallet => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
        };
    }
}
