<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Domain\Perfumes\Enums\PerfumeOrderStatus;
use App\Domain\Perfumes\Enums\PerfumePaymentMethod;
use App\Domain\Perfumes\Enums\PerfumePaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerfumeOrder extends Model
{
    use HasUlids;

    protected $fillable = [
        'order_number',
        'perfume_retailer_id',
        'subtotal',
        'discount',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'discount'       => 'decimal:2',
            'total'          => 'decimal:2',
            'status'         => PerfumeOrderStatus::class,
            'payment_method' => PerfumePaymentMethod::class,
            'payment_status' => PerfumePaymentStatus::class,
        ];
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(PerfumeRetailer::class, 'perfume_retailer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PerfumeOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PerfumePayment::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'PF-' . now()->format('Ym');
        $last = static::where('order_number', 'like', "{$prefix}%")
            ->orderByDesc('order_number')
            ->value('order_number');

        $next = $last ? (int) substr($last, -5) + 1 : 1;

        return $prefix . '-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->total - $this->total_paid;
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items()->sum(\DB::raw('cost_price * quantity'));
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total - $this->total_cost;
    }

    public function recalculatePaymentStatus(): void
    {
        $paid = $this->total_paid;

        if ($paid <= 0) {
            $this->payment_status = PerfumePaymentStatus::Pending;
        } elseif ($paid >= (float) $this->total) {
            $this->payment_status = PerfumePaymentStatus::Paid;
        } else {
            $this->payment_status = PerfumePaymentStatus::Partial;
        }

        $this->save();
    }
}
