<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\ConsignmentStock\Enums\ConsignmentStatus;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentStockItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'name',
        'model',
        'storage',
        'color',
        'imei',
        'supplier_cost',
        'suggested_price',
        'quantity',
        'available_quantity',
        'status',
        'notes',
        'received_at',
        'sold_at',
        'sale_id',
    ];

    protected function casts(): array
    {
        return [
            'supplier_cost' => 'decimal:2',
            'suggested_price' => 'decimal:2',
            'quantity' => 'integer',
            'available_quantity' => 'integer',
            'status' => ConsignmentStatus::class,
            'received_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ConsignmentStockMovement::class, 'consignment_item_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Available);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Sold);
    }

    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Returned);
    }

    public function scopeBySupplier(Builder $query, string $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('model', 'like', "%{$term}%")
              ->orWhere('imei', 'like', "%{$term}%")
              ->orWhere('color', 'like', "%{$term}%");
        });
    }

    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        if ($this->storage) {
            $parts[] = $this->storage;
        }
        if ($this->color) {
            $parts[] = $this->color;
        }

        return implode(' ', $parts);
    }

    public function getFormattedSupplierCostAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->supplier_cost, 2, ',', '.');
    }

    public function isAvailable(): bool
    {
        return $this->status === ConsignmentStatus::Available && $this->available_quantity > 0;
    }

    public function markAsSold(string $saleId): void
    {
        $this->update([
            'status' => ConsignmentStatus::Sold,
            'available_quantity' => 0,
            'sold_at' => now(),
            'sale_id' => $saleId,
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => ConsignmentStatus::Available,
            'available_quantity' => $this->quantity,
            'sold_at' => null,
            'sale_id' => null,
        ]);
    }
}
