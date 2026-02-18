<?php

declare(strict_types=1);

namespace App\Domain\B2B\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class B2BOrderItem extends Model
{
    use HasUlids;

    protected $table = 'b2b_order_items';

    protected $fillable = [
        'b2b_order_id',
        'b2b_product_id',
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
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(B2BOrder::class, 'b2b_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(B2BProduct::class, 'b2b_product_id');
    }

    public function getProfitAttribute(): float
    {
        return ((float) $this->unit_price - (float) $this->cost_price) * $this->quantity;
    }
}
