<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

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
        'imei',
        'estimated_value',
        'condition',
        'notes',
        'status',
        'product_id',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'condition' => TradeInCondition::class,
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
