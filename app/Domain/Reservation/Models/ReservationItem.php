<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Models;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reservation_id',
        'product_id',
        'cost_price',
        'sale_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function getProfitAttribute(): float
    {
        return ((float) $this->sale_price - (float) $this->cost_price) * $this->quantity;
    }
}
