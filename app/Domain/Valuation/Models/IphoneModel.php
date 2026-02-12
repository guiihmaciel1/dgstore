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
        'release_year',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'storages' => 'array',
            'colors' => 'array',
            'release_year' => 'integer',
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

    /**
     * Calcula a idade do modelo em anos (relativa ao ano atual).
     */
    public function getAgeInYearsAttribute(): int
    {
        return (int) now()->year - ($this->release_year ?? 2024);
    }

    /**
     * Fator de depreciação para estimar o preço de usado a partir do preço de novo.
     *
     * Formula: 0.80 - (idade_em_anos * 0.05), com piso de 0.45.
     *
     * Baseado em dados reais do mercado brasileiro (2025-2026):
     *   iPhone 16 (0 anos) → 0.80 (recém-lançado, aberto = 80% do novo)
     *   iPhone 15 (1 ano)  → 0.75
     *   iPhone 14 (2 anos) → 0.70
     *   ...continuando até o piso de 0.45
     */
    public function depreciationFactor(): float
    {
        $age = $this->age_in_years;
        $factor = 0.80 - ($age * 0.05);

        return max($factor, 0.45);
    }
}
