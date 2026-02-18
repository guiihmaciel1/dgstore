<?php

declare(strict_types=1);

namespace App\Domain\B2B\Models;

use App\Domain\B2B\Enums\B2BProductCondition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class B2BProduct extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'b2b_products';

    protected $fillable = [
        'name',
        'model',
        'storage',
        'color',
        'condition',
        'cost_price',
        'wholesale_price',
        'stock_quantity',
        'photo',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'condition' => B2BProductCondition::class,
            'cost_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(B2BOrderItem::class, 'b2b_product_id');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('active', true)->where('stock_quantity', '>', 0);
    }

    // Atributos calculados

    public function getProfitAttribute(): float
    {
        return (float) $this->wholesale_price - (float) $this->cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->cost_price <= 0) {
            return 0;
        }

        return (($this->profit) / (float) $this->cost_price) * 100;
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->cost_price, 2, ',', '.');
    }

    public function getFormattedWholesalePriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->wholesale_price, 2, ',', '.');
    }

    public function getFormattedProfitAttribute(): string
    {
        return 'R$ ' . number_format($this->profit, 2, ',', '.');
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
     * Retorna a URL da foto com caminho relativo, independente do APP_URL.
     * Suporta:
     *   - "images/b2b-products/..."  → /images/b2b-products/...   (novo padrão, public/)
     *   - "b2b-products/..."         → /images/b2b-products/...   (formato antigo, mapeado para public/)
     *   - qualquer outro caminho     → /storage/...               (uploads via admin)
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'images/')) {
            return '/' . ltrim($this->photo, '/');
        }

        // Formato antigo sem prefixo "images/" — os arquivos estão em public/images/
        if (str_starts_with($this->photo, 'b2b-products/')) {
            return '/images/' . ltrim($this->photo, '/');
        }

        // Uploads enviados pelo admin (storage/app/public/)
        return '/storage/' . ltrim($this->photo, '/');
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'storage' => $this->storage,
            'color' => $this->color,
            'condition' => $this->condition->value,
            'wholesale_price' => $this->wholesale_price,
            'cost_price' => $this->cost_price,
        ];
    }
}
