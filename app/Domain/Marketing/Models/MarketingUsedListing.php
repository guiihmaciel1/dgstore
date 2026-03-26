<?php

declare(strict_types=1);

namespace App\Domain\Marketing\Models;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingUsedListing extends Model
{
    use HasUlids;

    protected $fillable = [
        'product_id',
        'cost_price',
        'trade_in_price',
        'resale_price',
        'final_price',
        'has_box',
        'has_cable',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'trade_in_price' => 'decimal:2',
            'resale_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
