<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeReservationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfumeReservation extends Model
{
    use HasUlids;

    protected $fillable = [
        'reservation_number',
        'perfume_customer_id',
        'perfume_product_id',
        'product_description',
        'user_id',
        'product_price',
        'deposit_amount',
        'deposit_paid',
        'status',
        'expires_at',
        'converted_perfume_sale_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'product_price'  => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'deposit_paid'   => 'decimal:2',
            'status'         => PerfumeReservationStatus::class,
            'expires_at'     => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(PerfumeCustomer::class, 'perfume_customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(PerfumeProduct::class, 'perfume_product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(PerfumeSale::class, 'converted_perfume_sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PerfumeReservationPayment::class);
    }

    public static function generateReservationNumber(): string
    {
        $prefix = 'PFRES-' . now()->format('Ym');
        $last = static::where('reservation_number', 'like', "{$prefix}%")
            ->orderByDesc('reservation_number')
            ->value('reservation_number');

        $next = $last ? (int) substr($last, -5) + 1 : 1;

        return $prefix . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->deposit_paid;
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->deposit_amount - $this->total_paid;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === PerfumeReservationStatus::Active;
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', PerfumeReservationStatus::Active);
    }

    public function scopeExpired(Builder $query): void
    {
        $query->where('status', PerfumeReservationStatus::Active)
              ->where('expires_at', '<', now());
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->deposit_amount <= 0) {
            return 0;
        }

        return (int) min(100, ($this->deposit_paid / $this->deposit_amount) * 100);
    }
}
