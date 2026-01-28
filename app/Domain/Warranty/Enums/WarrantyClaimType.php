<?php

declare(strict_types=1);

namespace App\Domain\Warranty\Enums;

enum WarrantyClaimType: string
{
    case Supplier = 'supplier';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Supplier => 'Acionamento ao Fornecedor',
            self::Customer => 'Acionamento pelo Cliente',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Supplier => 'Fornecedor',
            self::Customer => 'Cliente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Supplier => 'blue',
            self::Customer => 'purple',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Supplier => 'Loja acionou garantia junto ao fornecedor',
            self::Customer => 'Cliente acionou garantia na loja',
        };
    }
}
