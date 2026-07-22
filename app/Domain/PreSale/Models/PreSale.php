<?php

declare(strict_types=1);

namespace App\Domain\PreSale\Models;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Customer\Models\Customer;
use App\Domain\PreSale\Enums\PreSaleStatus;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PreSale extends Model
{
    use HasUlids;

    protected $fillable = [
        'pre_sale_number',
        'customer_id',
        'seller_id',
        'seller_name',
        'product_id',
        'consignment_item_id',
        'product_snapshot',
        'product_imei',
        'unit_price',
        'cost_price',
        'condition',
        'down_payment',
        'down_payment_method',
        'payment_method',
        'installments',
        'card_gross_amount',
        'card_net_amount',
        'card_fee_rate',
        'trade_in_device',
        'trade_in_value',
        'final_balance',
        'notes',
        'status',
        'converted_sale_id',
        'converted_at',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'trade_in_device' => 'array',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'card_gross_amount' => 'decimal:2',
            'card_net_amount' => 'decimal:2',
            'card_fee_rate' => 'decimal:2',
            'trade_in_value' => 'decimal:2',
            'final_balance' => 'decimal:2',
            'installments' => 'integer',
            'status' => PreSaleStatus::class,
            'converted_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PreSale $preSale) {
            if (empty($preSale->pre_sale_number)) {
                $preSale->pre_sale_number = self::generateNumber();
            }
        });
    }

    // Relacionamentos

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function consignmentItem(): BelongsTo
    {
        return $this->belongsTo(ConsignmentStockItem::class, 'consignment_item_id');
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    // Scopes

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PreSaleStatus::Pending);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('pre_sale_number', 'like', "%{$term}%")
              ->orWhere('product_imei', 'like', "%{$term}%")
              ->orWhere('seller_name', 'like', "%{$term}%")
              ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"));
        });
    }

    // Helpers

    public function isPending(): bool
    {
        return $this->status === PreSaleStatus::Pending;
    }

    public function isConverted(): bool
    {
        return $this->status === PreSaleStatus::Converted;
    }

    public function isCancelled(): bool
    {
        return $this->status === PreSaleStatus::Cancelled;
    }

    public function isFromOwnStock(): bool
    {
        return $this->product_id !== null;
    }

    public function isFromConsignment(): bool
    {
        return $this->consignment_item_id !== null;
    }

    // Accessors

    public function getProductNameAttribute(): string
    {
        return $this->product_snapshot['name'] ?? 'Produto removido';
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->unit_price, 2, ',', '.');
    }

    public function getFormattedDownPaymentAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->down_payment, 2, ',', '.');
    }

    public function getFormattedFinalBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->final_balance, 2, ',', '.');
    }

    public function getFormattedTradeInValueAttribute(): string
    {
        return 'R$ ' . number_format((float) ($this->trade_in_value ?? 0), 2, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'pix' => 'PIX',
            'cash' => 'Dinheiro',
            'credit_card' => 'Cartão de Crédito',
            default => $this->payment_method ?? '-',
        };
    }

    public function getDownPaymentMethodLabelAttribute(): string
    {
        return match ($this->down_payment_method) {
            'pix' => 'PIX',
            'cash' => 'Dinheiro',
            default => $this->down_payment_method ?? '-',
        };
    }

    // Gerador de número

    public static function generateNumber(): string
    {
        $prefix = 'PV';
        $yearMonth = now()->format('Ym');
        $fullPrefix = $prefix . $yearMonth . '-';

        $lastNumber = self::where('pre_sale_number', 'like', $fullPrefix . '%')
            ->orderByDesc('pre_sale_number')
            ->value('pre_sale_number');

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        $number = sprintf('%s%05d', $fullPrefix, $sequence);

        if (self::where('pre_sale_number', $number)->exists()) {
            $maxSeq = DB::table('pre_sales')
                ->where('pre_sale_number', 'like', $fullPrefix . '%')
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(pre_sale_number, '-', -1) AS UNSIGNED)) as max_seq")
                ->value('max_seq');

            $number = sprintf('%s%05d', $fullPrefix, ($maxSeq ?? 0) + 1);
        }

        return $number;
    }
}
