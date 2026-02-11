<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PriceAverage extends Model
{
    use HasUlids;

    protected $fillable = [
        'iphone_model_id',
        'storage',
        'avg_price',
        'median_price',
        'min_price',
        'max_price',
        'suggested_buy_price',
        'sample_count',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'avg_price' => 'decimal:2',
            'median_price' => 'decimal:2',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'suggested_buy_price' => 'decimal:2',
            'sample_count' => 'integer',
            'calculated_at' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function iphoneModel(): BelongsTo
    {
        return $this->belongsTo(IphoneModel::class, 'iphone_model_id');
    }

    // Scopes

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('calculated_at');
    }

    public function scopeForModelAndStorage(Builder $query, string $modelId, string $storage): Builder
    {
        return $query->where('iphone_model_id', $modelId)
            ->where('storage', $storage);
    }

    // Accessors

    public function getFormattedAvgPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->avg_price, 2, ',', '.');
    }

    public function getFormattedMedianPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->median_price, 2, ',', '.');
    }

    public function getFormattedMinPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->min_price, 2, ',', '.');
    }

    public function getFormattedMaxPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->max_price, 2, ',', '.');
    }

    public function getFormattedSuggestedBuyPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->suggested_buy_price, 2, ',', '.');
    }

    /**
     * Retorna a "idade" dos dados em dias.
     */
    public function getDaysOldAttribute(): int
    {
        return (int) $this->calculated_at->diffInDays(now());
    }
}
