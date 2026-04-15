<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItemImage extends Model
{
    use HasUlids;

    protected $fillable = [
        'sale_item_id',
        'path',
        'original_name',
        'sort_order',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function getUrlAttribute(): ?string
    {
        if (! $this->path || ! $this->id) {
            return null;
        }

        return '/sales/item-images/' . $this->id . '/show';
    }
}
