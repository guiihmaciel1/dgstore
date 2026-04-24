<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use App\Domain\Checklist\Models\DeviceChecklist;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Enums\TradeInCondition;
use App\Domain\Sale\Enums\TradeInStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TradeIn extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'sale_id',
        'device_name',
        'device_model',
        'category',
        'storage',
        'color',
        'imei',
        'estimated_value',
        'cost_price',
        'sale_price',
        'resale_price',
        'condition',
        'battery_health',
        'has_box',
        'has_cable',
        'notes',
        'status',
        'product_id',
        'checklist_id',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'resale_price' => 'decimal:2',
            'condition' => TradeInCondition::class,
            'battery_health' => 'integer',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
            'status' => TradeInStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DeviceChecklist::class, 'checklist_id');
    }

    // Scopes

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TradeInStatus::Pending);
    }

    public function scopeProcessed(Builder $query): Builder
    {
        return $query->where('status', TradeInStatus::Processed);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', TradeInStatus::Rejected);
    }

    // Métodos auxiliares

    public function isPending(): bool
    {
        return $this->status === TradeInStatus::Pending;
    }

    public function isProcessed(): bool
    {
        return $this->status === TradeInStatus::Processed;
    }

    public function isRejected(): bool
    {
        return $this->status === TradeInStatus::Rejected;
    }

    public function markAsProcessed(string $productId): void
    {
        $this->update([
            'status' => TradeInStatus::Processed,
            'product_id' => $productId,
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'status' => TradeInStatus::Rejected,
        ]);
    }

    public function getFormattedValueAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->estimated_value, 2, ',', '.');
    }

    public function getFullNameAttribute(): string
    {
        if ($this->device_model) {
            return "{$this->device_name} ({$this->device_model})";
        }

        return $this->device_name;
    }
}
