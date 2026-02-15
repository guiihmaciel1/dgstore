<?php

declare(strict_types=1);

namespace App\Domain\Product\Models;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Sale\Models\SaleItem;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Stock\Models\StockMovement;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'stock_quantity',
        'min_stock_alert',
        'supplier',
        'notes',
        'active',
        'reserved',
        'reserved_by',
    ];

    protected function casts(): array
    {
        return [
            'category' => ProductCategory::class,
            'condition' => ProductCondition::class,
            'cost_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
            'active' => 'boolean',
            'reserved' => 'boolean',
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

    /**
     * Trade-in que originou este produto (se aplicável).
     */
    public function tradeIn(): HasOne
    {
        return $this->hasOne(TradeIn::class, 'product_id');
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

    /**
     * Verifica se o produto foi originado de um trade-in.
     */
    public function isFromTradeIn(): bool
    {
        return $this->tradeIn()->exists();
    }

    public function getFormattedCostPriceAttribute(): string
    {
        if ($this->cost_price === null) {
            return '-';
        }

        return 'R$ ' . number_format((float) $this->cost_price, 2, ',', '.');
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
        ];
    }
}
