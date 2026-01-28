<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Models;

use App\Domain\Product\Models\Product;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Quotation extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'user_id',
        'product_name',
        'unit_price',
        'quantity',
        'unit',
        'quoted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'decimal:2',
            'quoted_at' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    public function scopeForSupplier(Builder $query, string $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeForProduct(Builder $query, ?string $productId): Builder
    {
        if (!$productId) {
            return $query;
        }

        return $query->where('product_id', $productId);
    }

    public function scopeForProductName(Builder $query, ?string $productName): Builder
    {
        if (!$productName) {
            return $query;
        }

        return $query->where('product_name', 'like', "%{$productName}%");
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('quoted_at', today());
    }

    public function scopeBetweenDates(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->whereDate('quoted_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('quoted_at', '<=', $endDate);
        }

        return $query;
    }

    public function scopeLatestBySupplier(Builder $query): Builder
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('quotations')
                ->groupBy('supplier_id', 'product_name');
        });
    }

    public function scopeLatestPricePerSupplier(Builder $query, string $productName): Builder
    {
        return $query->where('product_name', $productName)
            ->whereIn('id', function ($subquery) use ($productName) {
                $subquery->selectRaw('MAX(id)')
                    ->from('quotations')
                    ->where('product_name', $productName)
                    ->groupBy('supplier_id');
            });
    }

    // Accessors

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->unit_price, 2, ',', '.');
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) $this->unit_price * (float) $this->quantity;
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->total_price, 2, ',', '.');
    }

    public function getFormattedQuantityAttribute(): string
    {
        $qty = (float) $this->quantity;
        
        if ($qty == floor($qty)) {
            return number_format($qty, 0, ',', '.') . ' ' . $this->unit;
        }

        return number_format($qty, 2, ',', '.') . ' ' . $this->unit;
    }
}
