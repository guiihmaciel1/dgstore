<?php

declare(strict_types=1);

namespace App\Domain\Product\Models;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Stock\Models\StockMovement;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'model',
        'storage',
        'color',
        'condition',
        'imei',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'min_stock_alert',
        'supplier',
        'notes',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'category' => ProductCategory::class,
            'condition' => ProductCondition::class,
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeIphones(Builder $query): Builder
    {
        return $query->where('category', ProductCategory::Smartphone);
    }

    public function scopeAccessories(Builder $query): Builder
    {
        return $query->where('category', ProductCategory::Accessory);
    }

    public function scopeServices(Builder $query): Builder
    {
        return $query->where('category', ProductCategory::Service);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Métodos auxiliares

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function isIphone(): bool
    {
        return $this->category === ProductCategory::Smartphone;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }

        return (($this->sale_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->cost_price, 2, ',', '.');
    }

    public function getFormattedSalePriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->sale_price, 2, ',', '.');
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

        return implode(' - ', $parts);
    }

    /**
     * Retorna um snapshot do produto para ser salvo na venda
     */
    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'category' => $this->category->value,
            'model' => $this->model,
            'storage' => $this->storage,
            'color' => $this->color,
            'condition' => $this->condition->value,
            'imei' => $this->imei,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
        ];
    }
}
