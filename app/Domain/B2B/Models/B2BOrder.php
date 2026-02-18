<?php

declare(strict_types=1);

namespace App\Domain\B2B\Models;

use App\Domain\B2B\Enums\B2BOrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class B2BOrder extends Model
{
    use HasUlids;

    protected $table = 'b2b_orders';

    protected $fillable = [
        'order_number',
        'b2b_retailer_id',
        'subtotal',
        'total',
        'status',
        'payment_method',
        'pix_code',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => B2BOrderStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(B2BRetailer::class, 'b2b_retailer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(B2BOrderItem::class, 'b2b_order_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->total, 2, ',', '.');
    }

    public function getTotalProfitAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return ((float) $item->unit_price - (float) $item->cost_price) * $item->quantity;
        });
    }

    public function getFormattedTotalProfitAttribute(): string
    {
        return 'R$ ' . number_format($this->total_profit, 2, ',', '.');
    }

    public function isPendingPayment(): bool
    {
        return $this->status === B2BOrderStatus::PendingPayment;
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'B2B-' . now()->format('Ym');
        $lastOrder = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('order_number')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public static function generatePixCode(): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 8; $j++) {
                $segment .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $segments[] = $segment;
        }

        return implode('.', $segments);
    }
}
