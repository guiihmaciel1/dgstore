<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Models;

use App\Domain\Valuation\Enums\ListingSource;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class MarketListing extends Model
{
    use HasUlids;

    protected $fillable = [
        'iphone_model_id',
        'storage',
        'title',
        'price',
        'url',
        'source',
        'location',
        'scraped_at',
    ];

    protected function casts(): array
    {
        return [
            'source' => ListingSource::class,
            'price' => 'decimal:2',
            'scraped_at' => 'date',
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

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('scraped_at', '>=', now()->subDays($days)->toDateString());
    }

    public function scopeForModel(Builder $query, string $modelId, ?string $storage = null): Builder
    {
        $query->where('iphone_model_id', $modelId);

        if ($storage) {
            $query->where('storage', $storage);
        }

        return $query;
    }

    // Accessors

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->price, 2, ',', '.');
    }
}
