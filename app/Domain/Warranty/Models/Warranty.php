<?php

declare(strict_types=1);

namespace App\Domain\Warranty\Models;

use App\Domain\Sale\Models\SaleItem;
use App\Domain\Warranty\Enums\WarrantyClaimStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Warranty extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'sale_item_id',
        'supplier_warranty_months',
        'customer_warranty_months',
        'supplier_warranty_until',
        'customer_warranty_until',
        'imei',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'supplier_warranty_months' => 'integer',
            'customer_warranty_months' => 'integer',
            'supplier_warranty_until' => 'date',
            'customer_warranty_until' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    // Scopes

    public function scopeSupplierExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('supplier_warranty_until')
            ->where('supplier_warranty_until', '>=', now())
            ->where('supplier_warranty_until', '<=', now()->addDays($days));
    }

    public function scopeCustomerExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('customer_warranty_until')
            ->where('customer_warranty_until', '>=', now())
            ->where('customer_warranty_until', '<=', now()->addDays($days));
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where(function (Builder $q) use ($days) {
            $q->supplierExpiringSoon($days)
              ->orWhere(function (Builder $q2) use ($days) {
                  $q2->customerExpiringSoon($days);
              });
        });
    }

    public function scopeSupplierActive(Builder $query): Builder
    {
        return $query->whereNotNull('supplier_warranty_until')
            ->where('supplier_warranty_until', '>=', now());
    }

    public function scopeCustomerActive(Builder $query): Builder
    {
        return $query->whereNotNull('customer_warranty_until')
            ->where('customer_warranty_until', '>=', now());
    }

    public function scopeWithOpenClaims(Builder $query): Builder
    {
        return $query->whereHas('claims', function (Builder $q) {
            $q->whereIn('status', [WarrantyClaimStatus::Opened, WarrantyClaimStatus::InProgress]);
        });
    }

    // Accessors

    public function getIsSupplierWarrantyActiveAttribute(): bool
    {
        return $this->supplier_warranty_until && $this->supplier_warranty_until->isFuture();
    }

    public function getIsCustomerWarrantyActiveAttribute(): bool
    {
        return $this->customer_warranty_until && $this->customer_warranty_until->isFuture();
    }

    public function getSupplierDaysRemainingAttribute(): ?int
    {
        if (!$this->supplier_warranty_until) {
            return null;
        }

        $days = now()->diffInDays($this->supplier_warranty_until, false);
        return max(0, $days);
    }

    public function getCustomerDaysRemainingAttribute(): ?int
    {
        if (!$this->customer_warranty_until) {
            return null;
        }

        $days = now()->diffInDays($this->customer_warranty_until, false);
        return max(0, $days);
    }

    public function getProductNameAttribute(): string
    {
        return $this->saleItem?->product_name ?? 'Produto não encontrado';
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->saleItem?->sale?->customer?->name;
    }

    public function getSaleNumberAttribute(): ?string
    {
        return $this->saleItem?->sale?->sale_number;
    }

    public function getOpenClaimsCountAttribute(): int
    {
        return $this->claims()->whereIn('status', [WarrantyClaimStatus::Opened, WarrantyClaimStatus::InProgress])->count();
    }

    // Métodos auxiliares

    public function isSupplierWarrantyExpiringSoon(int $days = 30): bool
    {
        if (!$this->supplier_warranty_until) {
            return false;
        }

        return $this->supplier_warranty_until->isBetween(now(), now()->addDays($days));
    }

    public function isCustomerWarrantyExpiringSoon(int $days = 30): bool
    {
        if (!$this->customer_warranty_until) {
            return false;
        }

        return $this->customer_warranty_until->isBetween(now(), now()->addDays($days));
    }

    public function canClaimSupplierWarranty(): bool
    {
        return $this->is_supplier_warranty_active;
    }

    public function canClaimCustomerWarranty(): bool
    {
        return $this->is_customer_warranty_active;
    }
}
