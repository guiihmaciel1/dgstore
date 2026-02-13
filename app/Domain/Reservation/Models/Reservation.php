<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Models\Product;
use App\Domain\Reservation\Enums\ReservationStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Reservation extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reservation_number',
        'customer_id',
        'product_id',
        'product_description',
        'source',
        'user_id',
        'status',
        'product_price',
        'cost_price',
        'deposit_amount',
        'deposit_paid',
        'expires_at',
        'converted_sale_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'product_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'deposit_paid' => 'decimal:2',
            'expires_at' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Reservation $reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = self::generateReservationNumber();
            }
        });
    }

    // Relacionamentos

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id')->withTrashed();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ReservationPayment::class);
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Active);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Expired);
    }

    public function scopeExpiringSoon(Builder $query, int $days = 3): Builder
    {
        return $query->where('status', ReservationStatus::Active)
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('expires_at', '>=', now());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', ReservationStatus::Active)
            ->where('expires_at', '<', now());
    }

    // Accessors

    public static function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $yearMonth = now()->format('Ym');
        $expectedPrefix = $prefix . $yearMonth . '-';

        $lastReservation = self::where('reservation_number', 'like', $expectedPrefix . '%')
            ->orderByRaw("CAST(SUBSTRING(reservation_number, ?) AS UNSIGNED) DESC", [strlen($expectedPrefix) + 1])
            ->first();

        if ($lastReservation && preg_match('/-(\d+)$/', $lastReservation->reservation_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $count = self::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            $sequence = $count + 1;
        }

        $number = sprintf('%s-%04d', $prefix . $yearMonth, $sequence);
        while (self::where('reservation_number', $number)->exists()) {
            $sequence++;
            $number = sprintf('%s-%04d', $prefix . $yearMonth, $sequence);
        }

        return $number;
    }

    public function getProductNameAttribute(): string
    {
        if ($this->product) {
            return $this->product->full_name ?? $this->product->name;
        }

        return $this->product_description ?? 'Produto não especificado';
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->product_price - $this->deposit_paid);
    }

    public function getDepositPendingAttribute(): float
    {
        return max(0, $this->deposit_amount - $this->deposit_paid);
    }

    public function getFormattedProductPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->product_price, 2, ',', '.');
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->cost_price, 2, ',', '.');
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->product_price - (float) $this->cost_price;
    }

    public function getFormattedProfitAttribute(): string
    {
        return 'R$ ' . number_format($this->profit, 2, ',', '.');
    }

    public function getFormattedDepositAmountAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->deposit_amount, 2, ',', '.');
    }

    public function getFormattedDepositPaidAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->deposit_paid, 2, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'R$ ' . number_format($this->remaining_amount, 2, ',', '.');
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if ($this->expires_at->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === ReservationStatus::Active && $this->expires_at->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if ($this->status !== ReservationStatus::Active) {
            return false;
        }

        return $this->expires_at->isBetween(now(), now()->addDays(3));
    }

    public function getDepositPercentPaidAttribute(): float
    {
        if ($this->deposit_amount <= 0) {
            return 100;
        }

        return min(100, ($this->deposit_paid / $this->deposit_amount) * 100);
    }

    // Métodos auxiliares

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function canConvert(): bool
    {
        return $this->status->canConvert();
    }

    public function canCancel(): bool
    {
        return $this->status->canCancel();
    }

    public function canReceivePayment(): bool
    {
        return $this->isActive() && $this->deposit_paid < $this->deposit_amount;
    }

    public function addPayment(float $amount): void
    {
        $newTotal = min($this->deposit_amount, $this->deposit_paid + $amount);
        $this->update(['deposit_paid' => $newTotal]);
    }

    public function markAsConverted(string $saleId): void
    {
        $this->update([
            'status' => ReservationStatus::Converted,
            'converted_sale_id' => $saleId,
        ]);

        // Libera o produto
        $this->product?->update(['reserved' => false, 'reserved_by' => null]);
    }

    public function cancel(): void
    {
        $this->update(['status' => ReservationStatus::Cancelled]);

        // Libera o produto
        $this->product?->update(['reserved' => false, 'reserved_by' => null]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => ReservationStatus::Expired]);

        // Libera o produto
        $this->product?->update(['reserved' => false, 'reserved_by' => null]);
    }

    public function reserveProduct(): void
    {
        $this->product?->update([
            'reserved' => true,
            'reserved_by' => $this->id,
        ]);
    }
}
