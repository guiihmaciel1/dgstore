<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfumeSaleItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'perfume_sale_id',
        'perfume_product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'cost_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'quantity'         => 'integer',
            'unit_price'       => 'decimal:2',
            'cost_price'       => 'decimal:2',
            'subtotal'         => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PerfumeSale::class, 'perfume_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PerfumeProduct::class, 'perfume_product_id');
    }

    public function getProfitAttribute(): float
    {
        return (float) ($this->subtotal - ($this->cost_price * $this->quantity));
    }
}
