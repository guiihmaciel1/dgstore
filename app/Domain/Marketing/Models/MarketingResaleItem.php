<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketingResaleItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'resaleable_type',
        'resaleable_id',
        'resale_price',
        'battery_health',
        'warranty_until',
        'has_box',
        'has_cable',
        'notes',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'resale_price' => 'decimal:2',
            'battery_health' => 'integer',
            'warranty_until' => 'date',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
            'visible' => 'boolean',
        ];
    }

    public function resaleable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopeForConsignment(Builder $query): Builder
    {
        return $query->where('resaleable_type', ConsignmentStockItem::class);
    }

    public function scopeForProduct(Builder $query): Builder
    {
        return $query->where('resaleable_type', Product::class);
    }

    public function isConsignment(): bool
    {
        return $this->resaleable_type === ConsignmentStockItem::class;
    }

    public function isUsed(): bool
    {
        return $this->resaleable_type === Product::class;
    }
}
