<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfumeProduct extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'brand',
        'category',
        'size_ml',
        'cost_price',
        'sale_price',
        'stock_quantity',
        'photo',
        'barcode',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'category'       => PerfumeCategory::class,
            'cost_price'     => 'decimal:2',
            'sale_price'     => 'decimal:2',
            'stock_quantity' => 'integer',
            'active'         => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    public function samples(): HasMany
    {
        return $this->hasMany(PerfumeSample::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(PerfumeOrderItem::class);
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->sale_price - (float) $this->cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ((float) $this->sale_price <= 0) {
            return 0;
        }

        return round(($this->profit / (float) $this->sale_price) * 100, 1);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http')) {
            return $this->photo;
        }

        return asset('storage/' . $this->photo);
    }
}
