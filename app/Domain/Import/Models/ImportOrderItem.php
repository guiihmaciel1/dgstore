<?php

declare(strict_types=1);

namespace App\Domain\Import\Models;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportOrderItem extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'import_order_id',
        'product_id',
        'description',
        'quantity',
        'unit_cost',
        'received_quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'received_quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function importOrder(): BelongsTo
    {
        return $this->belongsTo(ImportOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Accessors

    public function getTotalCostAttribute(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return '$ ' . number_format((float) $this->unit_cost, 2, ',', '.');
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return '$ ' . number_format($this->total_cost, 2, ',', '.');
    }

    public function getPendingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->received_quantity);
    }

    public function getIsFullyReceivedAttribute(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    public function getIsPartiallyReceivedAttribute(): bool
    {
        return $this->received_quantity > 0 && $this->received_quantity < $this->quantity;
    }

    // Métodos auxiliares

    public function receive(int $quantity): void
    {
        $newReceived = min($this->quantity, $this->received_quantity + $quantity);
        $this->update(['received_quantity' => $newReceived]);
    }

    public function receiveAll(): void
    {
        $this->update(['received_quantity' => $this->quantity]);
    }
}
