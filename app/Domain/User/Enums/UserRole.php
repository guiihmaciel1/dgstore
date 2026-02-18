<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case AdminGeral    = 'admin_geral';
    case AdminB2B      = 'admin_b2b';
    case AdminPerfumes = 'admin_perfumes';
    case Seller        = 'seller';
    case SellerB2B     = 'seller_b2b';

    public function label(): string
    {
        return match ($this) {
            self::AdminGeral    => 'Admin Geral',
            self::AdminB2B      => 'Admin Distribuidora',
            self::AdminPerfumes => 'Admin Perfumes',
            self::Seller        => 'Vendedor',
            self::SellerB2B     => 'Vendedor B2B',
        };
    }

    public function canAccessDGStore(): bool
    {
        return in_array($this, [self::AdminGeral, self::Seller]);
    }

    public function canAccessB2BAdmin(): bool
    {
        return in_array($this, [self::AdminGeral, self::AdminB2B]);
    }

    public function canAccessPerfumesAdmin(): bool
    {
        return in_array($this, [self::AdminGeral, self::AdminPerfumes]);
    }

    /** Mantém retrocompatibilidade com isAdmin() nas views legadas */
    public function isAdmin(): bool
    {
        return $this->canAccessB2BAdmin();
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::AdminGeral    => 'purple',
            self::AdminB2B      => 'blue',
            self::AdminPerfumes => 'pink',
            self::Seller        => 'green',
            self::SellerB2B     => 'indigo',
        };
    }
}
