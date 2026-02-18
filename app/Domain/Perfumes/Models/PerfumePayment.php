<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfumePayment extends Model
{
    use HasUlids;

    protected $fillable = [
        'perfume_order_id',
        'amount',
        'method',
        'reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PerfumeOrder::class, 'perfume_order_id');
    }
}
