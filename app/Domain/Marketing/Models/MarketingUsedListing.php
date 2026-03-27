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
        'final_price',
        'battery_health',
        'has_box',
        'has_cable',
        'notes',
        'visible',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'battery_health' => 'integer',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
            'visible' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
