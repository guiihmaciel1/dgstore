<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Sale extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'user_id',
        'subtotal',
        'discount',
        'trade_in_value',
        'cash_payment',
        'card_payment',
        'cash_payment_method',
        'total',
        'payment_method',
        'payment_status',
        'installments',
        'notes',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'trade_in_value' => 'decimal:2',
            'cash_payment' => 'decimal:2',
            'card_payment' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'installments' => 'integer',
            'sold_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Sale $sale) {
            if (empty($sale->sale_number)) {
                $sale->sale_number = self::generateSaleNumber();
            }
        });
    }

    // Relacionamentos

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id');
    }

    public function tradeIn(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TradeIn::class);
    }

    // Scopes

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', PaymentStatus::Paid);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', PaymentStatus::Pending);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('payment_status', PaymentStatus::Cancelled);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('sold_at', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('sold_at', now()->month)
            ->whereYear('sold_at', now()->year);
    }

    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('sold_at', [$startDate, $endDate]);
    }

    // Métodos auxiliares

    public static function generateSaleNumber(): string
    {
        $prefix = 'DG';
        $yearMonth = now()->format('Ym');
        $expectedPrefix = $prefix . $yearMonth . '-';

        // Busca a última venda do mês com formato correto (DG202602-NNNNN)
        $lastSale = self::withTrashed()
            ->where('sale_number', 'like', $expectedPrefix . '%')
            ->orderByRaw("CAST(SUBSTRING(sale_number, ?) AS UNSIGNED) DESC", [strlen($expectedPrefix) + 1])
            ->first();

        if ($lastSale && preg_match('/-(\d+)$/', $lastSale->sale_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            // Fallback: conta vendas do mês para evitar colisão com formatos antigos
            $count = self::withTrashed()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
            $sequence = $count + 1;
        }

        // Garante unicidade em caso de race condition
        $saleNumber = sprintf('%s-%05d', $prefix . $yearMonth, $sequence);
        while (self::withTrashed()->where('sale_number', $saleNumber)->exists()) {
            $sequence++;
            $saleNumber = sprintf('%s-%05d', $prefix . $yearMonth, $sequence);
        }

        return $saleNumber;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }

    public function isPending(): bool
    {
        return $this->payment_status === PaymentStatus::Pending;
    }

    public function isCancelled(): bool
    {
        return $this->payment_status === PaymentStatus::Cancelled;
    }

    public function canBeCancelled(): bool
    {
        return !$this->isCancelled();
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->total, 2, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->subtotal, 2, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->discount, 2, ',', '.');
    }

    public function getFormattedTradeInValueAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->trade_in_value, 2, ',', '.');
    }

    public function getFormattedCashPaymentAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->cash_payment, 2, ',', '.');
    }

    public function getFormattedCardPaymentAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->card_payment, 2, ',', '.');
    }

    public function hasTradeIn(): bool
    {
        return (float) $this->trade_in_value > 0;
    }

    public function hasMixedPayment(): bool
    {
        $methods = 0;
        if ((float) $this->trade_in_value > 0) $methods++;
        if ((float) $this->cash_payment > 0) $methods++;
        if ((float) $this->card_payment > 0) $methods++;
        return $methods > 1;
    }

    public function getCashPaymentMethodLabelAttribute(): ?string
    {
        return match ($this->cash_payment_method) {
            'cash' => 'Dinheiro',
            'pix' => 'PIX',
            default => null,
        };
    }

    public function getInstallmentValueAttribute(): float
    {
        if ($this->installments <= 0) {
            return (float) $this->total;
        }

        if ($this->installments === 1) {
            return (float) $this->total;
        }

        return (float) $this->total / $this->installments;
    }

    public function getFormattedInstallmentValueAttribute(): string
    {
        return 'R$ ' . number_format($this->installment_value, 2, ',', '.');
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal - $this->discount;
    }

    /**
     * Calcula o custo total dos produtos vendidos (CMV)
     * a partir do cost_price salvo no product_snapshot.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            $costPrice = $item->product_snapshot['cost_price'] ?? 0;
            return (float) $costPrice * $item->quantity;
        });
    }

    /**
     * Calcula o lucro bruto da venda: total recebido - custo dos produtos
     */
    public function getProfitAttribute(): float
    {
        return (float) $this->total - $this->total_cost;
    }

    public function getFormattedProfitAttribute(): string
    {
        return 'R$ ' . number_format($this->profit, 2, ',', '.');
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return 'R$ ' . number_format($this->total_cost, 2, ',', '.');
    }
}
