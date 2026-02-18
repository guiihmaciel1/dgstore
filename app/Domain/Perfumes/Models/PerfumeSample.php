<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeSampleStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfumeSample extends Model
{
    use HasUlids;

    protected $fillable = [
        'perfume_product_id',
        'perfume_retailer_id',
        'quantity',
        'delivered_at',
        'returned_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'       => PerfumeSampleStatus::class,
            'delivered_at' => 'datetime',
            'returned_at'  => 'datetime',
            'quantity'     => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PerfumeProduct::class, 'perfume_product_id');
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(PerfumeRetailer::class, 'perfume_retailer_id');
    }

    public function getDaysOutAttribute(): ?int
    {
        if (! $this->delivered_at || $this->status === PerfumeSampleStatus::Returned) {
            return null;
        }

        return (int) $this->delivered_at->diffInDays(now());
    }
}
