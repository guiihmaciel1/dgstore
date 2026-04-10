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
    case Intern        = 'intern';

    public function label(): string
    {
        return match ($this) {
            self::AdminGeral    => 'Admin Geral',
            self::AdminB2B      => 'Admin B2B',
            self::AdminPerfumes => 'Admin Perfumes',
            self::Seller        => 'Vendedor',
            self::SellerB2B     => 'Vendedor B2B',
            self::Intern        => 'Estagiária',
        };
    }

    public function canAccessDGStore(): bool
    {
        return in_array($this, [self::AdminGeral, self::Seller, self::Intern]);
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

    public function isAdminGeral(): bool
    {
        return $this === self::AdminGeral;
    }

    public function isIntern(): bool
    {
        return $this === self::Intern;
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::AdminGeral    => 'purple',
            self::AdminB2B      => 'blue',
            self::AdminPerfumes => 'pink',
            self::Seller        => 'green',
            self::SellerB2B     => 'indigo',
            self::Intern        => 'teal',
        };
    }
}
