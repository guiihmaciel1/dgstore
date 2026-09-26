<?php

declare(strict_types=1);

namespace App\Domain\PurchaseRequest\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\PurchaseRequest\Enums\DeliveryType;
use App\Domain\PurchaseRequest\Enums\PurchaseRequestStatus;
use App\Domain\PurchaseRequest\Enums\PurchaseRequestType;
use App\Domain\Sale\Models\Sale;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PurchaseRequest extends Model
{
    use HasUlids;

    protected $fillable = [
        'request_number',
        'customer_id',
        'seller_id',
        'seller_name',
        'type',
        'desired_product',
        'desired_model',
        'desired_storage',
        'desired_color',
        'desired_condition',
        'estimated_cost',
        'sale_price',
        'down_payment',
        'down_payment_method',
        'payment_method',
        'installments',
        'trade_in_device',
        'trade_in_value',
        'upgrade_difference',
        'delivery_type',
        'delivery_address',
        'delivery_time',
        'delivery_notes',
        'status',
        'approved_by',
        'approved_at',
        'purchased_at',
        'delivered_at',
        'converted_at',
        'converted_sale_id',
        'cancelled_at',
        'cancelled_reason',
        'expires_at',
        'notes',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => PurchaseRequestType::class,
            'status' => PurchaseRequestStatus::class,
            'delivery_type' => DeliveryType::class,
            'estimated_cost' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'trade_in_value' => 'decimal:2',
            'upgrade_difference' => 'decimal:2',
            'installments' => 'integer',
            'approved_at' => 'datetime',
            'purchased_at' => 'datetime',
            'delivered_at' => 'datetime',
            'converted_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PurchaseRequest $request) {
            if (empty($request->request_number)) {
                $request->request_number = self::generateNumber();
            }
            if (empty($request->expires_at)) {
                $request->expires_at = now()->addHours(48);
            }
        });
    }

    // Relacionamentos

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function convertedSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }

    // Scopes

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PurchaseRequestStatus::Pending);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PurchaseRequestStatus::Approved);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            PurchaseRequestStatus::Pending,
            PurchaseRequestStatus::Approved,
            PurchaseRequestStatus::Purchased,
            PurchaseRequestStatus::Delivered,
        ]);
    }

    public function scopeExpirable(Builder $query): Builder
    {
        return $query->where('status', PurchaseRequestStatus::Pending)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('request_number', 'like', "%{$term}%")
              ->orWhere('desired_product', 'like', "%{$term}%")
              ->orWhere('seller_name', 'like', "%{$term}%")
              ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"));
        });
    }

    // Status helpers

    public function isPending(): bool
    {
        return $this->status === PurchaseRequestStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === PurchaseRequestStatus::Approved;
    }

    public function isPurchased(): bool
    {
        return $this->status === PurchaseRequestStatus::Purchased;
    }

    public function isDelivered(): bool
    {
        return $this->status === PurchaseRequestStatus::Delivered;
    }

    public function isConverted(): bool
    {
        return $this->status === PurchaseRequestStatus::Converted;
    }

    public function isCancelled(): bool
    {
        return $this->status === PurchaseRequestStatus::Cancelled;
    }

    public function isRejected(): bool
    {
        return $this->status === PurchaseRequestStatus::Rejected;
    }

    public function isExpired(): bool
    {
        return $this->status === PurchaseRequestStatus::Expired;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    public function isUpgrade(): bool
    {
        return $this->type === PurchaseRequestType::Upgrade;
    }

    public function isDelivery(): bool
    {
        return $this->delivery_type === DeliveryType::Delivery;
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    public function canBePurchased(): bool
    {
        return $this->isApproved();
    }

    public function canBeDelivered(): bool
    {
        return $this->isPurchased();
    }

    public function canBeConverted(): bool
    {
        return $this->isDelivered();
    }

    public function canBeCancelled(): bool
    {
        return $this->isActive() && !$this->isDelivered() && !$this->isConverted();
    }

    // Accessors

    public function getFormattedSalePriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->sale_price, 2, ',', '.');
    }

    public function getFormattedDownPaymentAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->down_payment, 2, ',', '.');
    }

    public function getFormattedEstimatedCostAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->estimated_cost, 2, ',', '.');
    }

    public function getFormattedTradeInValueAttribute(): string
    {
        return 'R$ ' . number_format((float) ($this->trade_in_value ?? 0), 2, ',', '.');
    }

    public function getFormattedUpgradeDifferenceAttribute(): string
    {
        return 'R$ ' . number_format((float) ($this->upgrade_difference ?? 0), 2, ',', '.');
    }

    public function getRemainingBalanceAttribute(): float
    {
        $balance = (float) $this->sale_price - (float) $this->down_payment;

        if ($this->isUpgrade() && $this->trade_in_value) {
            $balance -= (float) $this->trade_in_value;
        }

        return max(0, $balance);
    }

    public function getFormattedRemainingBalanceAttribute(): string
    {
        return 'R$ ' . number_format($this->remaining_balance, 2, ',', '.');
    }

    public function getDownPaymentMethodLabelAttribute(): string
    {
        return match ($this->down_payment_method) {
            'pix' => 'PIX',
            'cash' => 'Dinheiro',
            default => $this->down_payment_method ?? '-',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'pix' => 'PIX',
            'cash' => 'Dinheiro',
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            default => $this->payment_method ?? '-',
        };
    }

    public function getDesiredConditionLabelAttribute(): string
    {
        return match ($this->desired_condition) {
            'new' => 'Novo',
            'used' => 'Seminovo',
            default => $this->desired_condition ?? '-',
        };
    }

    // Gerador de número

    public static function generateNumber(): string
    {
        $prefix = 'SC';
        $yearMonth = now()->format('Ym');
        $fullPrefix = $prefix . $yearMonth . '-';

        $lastNumber = self::where('request_number', 'like', $fullPrefix . '%')
            ->orderByDesc('request_number')
            ->value('request_number');

        if ($lastNumber && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        $number = sprintf('%s%05d', $fullPrefix, $sequence);

        if (self::where('request_number', $number)->exists()) {
            $maxSeq = DB::table('purchase_requests')
                ->where('request_number', 'like', $fullPrefix . '%')
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(request_number, '-', -1) AS UNSIGNED)) as max_seq")
                ->value('max_seq');

            $number = sprintf('%s%05d', $fullPrefix, ($maxSeq ?? 0) + 1);
        }

        return $number;
    }
}
