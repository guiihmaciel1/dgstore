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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function tradeIns(): HasMany
    {
        return $this->hasMany(TradeIn::class);
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
        $fullPrefix = $prefix . $yearMonth . '-';

        // Abordagem 1: busca o maior sale_number por ordenação de string
        // Funciona porque os números são zero-padded (00001, 00002...)
        $lastNumber = self::withTrashed()
            ->where('sale_number', 'like', $fullPrefix . '%')
            ->orderByDesc('sale_number')
            ->value('sale_number');

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            // Nenhuma venda no mês com formato novo, começar do 1
            $sequence = 1;
        }

        $saleNumber = sprintf('%s%05d', $fullPrefix, $sequence);

        // Verificação extra: se por alguma razão ainda existe, tenta via MAX direto no DB
        if (self::withTrashed()->where('sale_number', $saleNumber)->exists()) {
            Log::warning("generateSaleNumber: colisão detectada", [
                'tentativa' => $saleNumber,
                'lastNumber' => $lastNumber,
                'sequence' => $sequence,
            ]);

            // Fallback: busca MAX via query raw sem depender de ordering
            $maxSeq = DB::table('sales')
                ->where('sale_number', 'like', $fullPrefix . '%')
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(sale_number, '-', -1) AS UNSIGNED)) as max_seq")
                ->value('max_seq');

            $sequence = ($maxSeq ?? 0) + 1;
            $saleNumber = sprintf('%s%05d', $fullPrefix, $sequence);

            Log::info("generateSaleNumber: fallback gerou", [
                'maxSeq' => $maxSeq,
                'novoNumero' => $saleNumber,
            ]);
        }

        // Último recurso: adiciona timestamp para garantir unicidade
        if (self::withTrashed()->where('sale_number', $saleNumber)->exists()) {
            $saleNumber = $fullPrefix . now()->format('dHis');
            Log::warning("generateSaleNumber: usando timestamp como último recurso", [
                'numero' => $saleNumber,
            ]);
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
     * usando total_cost do item (cost_price + frete).
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->total_cost_value * $item->quantity;
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
