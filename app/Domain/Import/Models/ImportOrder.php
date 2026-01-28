<?php

declare(strict_types=1);

namespace App\Domain\Import\Models;

use App\Domain\Import\Enums\ImportOrderStatus;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ImportOrder extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'order_number',
        'supplier_id',
        'user_id',
        'status',
        'tracking_code',
        'estimated_cost',
        'actual_cost',
        'exchange_rate',
        'shipping_cost',
        'taxes',
        'ordered_at',
        'shipped_at',
        'estimated_arrival',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ImportOrderStatus::class,
            'estimated_cost' => 'decimal:2',
            'actual_cost' => 'decimal:2',
            'exchange_rate' => 'decimal:4',
            'shipping_cost' => 'decimal:2',
            'taxes' => 'decimal:2',
            'ordered_at' => 'date',
            'shipped_at' => 'date',
            'estimated_arrival' => 'date',
            'received_at' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ImportOrder $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    // Relacionamentos

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ImportOrderItem::class);
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ImportOrderStatus::activeStatuses());
    }

    public function scopeByStatus(Builder $query, ImportOrderStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeInTransit(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ImportOrderStatus::Shipped,
            ImportOrderStatus::InTransit,
            ImportOrderStatus::Customs,
        ]);
    }

    // Accessors

    public static function generateOrderNumber(): string
    {
        $prefix = 'IMP';
        $year = now()->format('Y');
        $month = now()->format('m');
        
        $lastOrder = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastOrder && preg_match('/(\d+)$/', $lastOrder->order_number, $matches)) {
            $sequence = (int) $matches[1] + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }

    public function getEstimatedTotalBrlAttribute(): float
    {
        return (float) ($this->estimated_cost * $this->exchange_rate) + $this->shipping_cost + $this->taxes;
    }

    public function getActualTotalBrlAttribute(): ?float
    {
        if ($this->actual_cost === null) {
            return null;
        }

        return (float) ($this->actual_cost * $this->exchange_rate) + $this->shipping_cost + $this->taxes;
    }

    public function getFormattedEstimatedCostAttribute(): string
    {
        return '$ ' . number_format((float) $this->estimated_cost, 2, ',', '.');
    }

    public function getFormattedActualCostAttribute(): string
    {
        if ($this->actual_cost === null) {
            return '-';
        }
        return '$ ' . number_format((float) $this->actual_cost, 2, ',', '.');
    }

    public function getFormattedEstimatedTotalBrlAttribute(): string
    {
        return 'R$ ' . number_format($this->estimated_total_brl, 2, ',', '.');
    }

    public function getFormattedActualTotalBrlAttribute(): string
    {
        $total = $this->actual_total_brl;
        if ($total === null) {
            return '-';
        }
        return 'R$ ' . number_format($total, 2, ',', '.');
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->shipping_cost, 2, ',', '.');
    }

    public function getFormattedTaxesAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->taxes, 2, ',', '.');
    }

    public function getCostDifferenceAttribute(): ?float
    {
        if ($this->actual_cost === null) {
            return null;
        }

        return $this->actual_total_brl - $this->estimated_total_brl;
    }

    public function getCostDifferencePercentAttribute(): ?float
    {
        if ($this->actual_cost === null || $this->estimated_total_brl == 0) {
            return null;
        }

        return (($this->actual_total_brl - $this->estimated_total_brl) / $this->estimated_total_brl) * 100;
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getTotalReceivedAttribute(): int
    {
        return $this->items->sum('received_quantity');
    }

    public function getDaysInTransitAttribute(): ?int
    {
        if (!$this->shipped_at) {
            return null;
        }

        $endDate = $this->received_at ?? now();
        return $this->shipped_at->diffInDays($endDate);
    }

    // Métodos auxiliares

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isReceived(): bool
    {
        return $this->status === ImportOrderStatus::Received;
    }

    public function isCancelled(): bool
    {
        return $this->status === ImportOrderStatus::Cancelled;
    }

    public function canAdvanceTo(ImportOrderStatus $newStatus): bool
    {
        return $this->status->canAdvanceTo($newStatus);
    }

    public function advanceStatus(ImportOrderStatus $newStatus): void
    {
        $data = ['status' => $newStatus];

        if ($newStatus === ImportOrderStatus::Shipped && !$this->shipped_at) {
            $data['shipped_at'] = now();
        }

        if ($newStatus === ImportOrderStatus::Received && !$this->received_at) {
            $data['received_at'] = now();
        }

        $this->update($data);
    }

    public function calculateItemsTotal(): float
    {
        return $this->items->sum(fn($item) => $item->quantity * $item->unit_cost);
    }

    public function updateEstimatedCost(): void
    {
        $this->update([
            'estimated_cost' => $this->calculateItemsTotal(),
        ]);
    }
}
