<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeSalePaymentMethod;
use App\Domain\Perfumes\Enums\PerfumeSalePaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfumeSale extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'sale_number',
        'perfume_customer_id',
        'user_id',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'payment_amount',
        'installments',
        'payment_status',
        'sold_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'discount'       => 'decimal:2',
            'total'          => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'installments'   => 'integer',
            'payment_method' => PerfumeSalePaymentMethod::class,
            'payment_status' => PerfumeSalePaymentStatus::class,
            'sold_at'        => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(PerfumeCustomer::class, 'perfume_customer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PerfumeSaleItem::class);
    }

    public static function generateSaleNumber(): string
    {
        $prefix = 'PFVD-' . now()->format('Ym');
        $last = static::where('sale_number', 'like', "{$prefix}%")
            ->orderByDesc('sale_number')
            ->value('sale_number');

        $next = $last ? (int) substr($last, -5) + 1 : 1;

        return $prefix . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items()->sum(\DB::raw('cost_price * quantity'));
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total - $this->total_cost;
    }

    public function getInstallmentValueAttribute(): float
    {
        if ($this->installments <= 0) {
            return 0;
        }

        return (float) $this->total / $this->installments;
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('payment_status', PerfumeSalePaymentStatus::Paid);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('payment_status', PerfumeSalePaymentStatus::Pending);
    }

    public function scopeToday(Builder $query): void
    {
        $query->whereDate('sold_at', today());
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->whereMonth('sold_at', now()->month)
              ->whereYear('sold_at', now()->year);
    }

    public function scopeBetweenDates(Builder $query, string $startDate, string $endDate): void
    {
        $query->whereBetween('sold_at', [$startDate, $endDate]);
    }
}
