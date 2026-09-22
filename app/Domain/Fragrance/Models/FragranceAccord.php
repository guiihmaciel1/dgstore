<?php

declare(strict_types=1);

namespace App\Domain\Fragrance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FragranceAccord extends Model
{
    protected $fillable = [
        'fragrance_product_id',
        'name',
        'percentage',
        'color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(FragranceProduct::class, 'fragrance_product_id');
    }
}
