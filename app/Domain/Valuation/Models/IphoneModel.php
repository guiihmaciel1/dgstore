<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class IphoneModel extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'slug',
        'storages',
        'colors',
        'search_term',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'storages' => 'array',
            'colors' => 'array',
            'active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function marketListings(): HasMany
    {
        return $this->hasMany(MarketListing::class, 'iphone_model_id');
    }

    public function priceAverages(): HasMany
    {
        return $this->hasMany(PriceAverage::class, 'iphone_model_id');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    // Métodos auxiliares

    /**
     * Retorna a média de preço mais recente para um storage específico.
     */
    public function latestPriceAverage(string $storage): ?PriceAverage
    {
        return $this->priceAverages()
            ->where('storage', $storage)
            ->latest('calculated_at')
            ->first();
    }
}
