<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketingUsedListing extends Model
{
    use HasUlids;

    protected $fillable = [
        'listable_type',
        'listable_id',
        'cost_price',
        'final_price',
        'battery_health',
        'has_box',
        'has_cable',
        'notes',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'battery_health' => 'integer',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
            'visible' => 'boolean',
        ];
    }

    public function listable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForProduct(Builder $query): Builder
    {
        return $query->where('listable_type', Product::class);
    }

    public function scopeForConsignment(Builder $query): Builder
    {
        return $query->where('listable_type', ConsignmentStockItem::class);
    }

    public function isProduct(): bool
    {
        return $this->listable_type === Product::class;
    }

    public function isConsignment(): bool
    {
        return $this->listable_type === ConsignmentStockItem::class;
    }

    public function images(): HasMany
    {
        return $this->hasMany(MarketingUsedListingImage::class)->orderBy('sort_order');
    }
}
