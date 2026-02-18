<?php

declare(strict_types=1);

namespace App\Domain\User\Models;

use App\Domain\Sale\Models\Sale;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\User\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUlids, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'active' => 'boolean',
        ];
    }

    // Relacionamentos

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Verificações de role

    public function isAdminGeral(): bool
    {
        return $this->role === UserRole::AdminGeral;
    }

    public function isAdminB2B(): bool
    {
        return $this->role === UserRole::AdminB2B;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::Seller;
    }

    public function isSellerB2B(): bool
    {
        return $this->role === UserRole::SellerB2B;
    }

    /** Mantém retrocompatibilidade com views/controllers que chamam isAdmin() */
    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    // Verificações de acesso por área

    public function canAccessDGStore(): bool
    {
        return $this->role->canAccessDGStore();
    }

    public function canAccessB2BAdmin(): bool
    {
        return $this->role->canAccessB2BAdmin();
    }

    public function isAdminPerfumes(): bool
    {
        return $this->role === UserRole::AdminPerfumes;
    }

    public function canAccessPerfumesAdmin(): bool
    {
        return $this->role->canAccessPerfumesAdmin();
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
