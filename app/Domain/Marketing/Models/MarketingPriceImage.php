<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MarketingPriceImage extends Model
{
    use HasUlids;

    protected $fillable = [
        'marketing_price_id',
        'path',
        'original_name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function marketingPrice(): BelongsTo
    {
        return $this->belongsTo(MarketingPrice::class);
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }

        return Storage::disk('public')->url($this->path);
    }
}
