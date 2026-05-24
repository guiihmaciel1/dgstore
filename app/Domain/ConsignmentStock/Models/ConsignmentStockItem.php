<?php

declare(strict_types=1);

namespace App\Domain\ConsignmentStock\Models;

use App\Domain\ConsignmentStock\Enums\ConsignmentStatus;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Domain\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentStockItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'supplier_id',
        'batch_id',
        'product_id',
        'name',
        'model',
        'storage',
        'color',
        'condition',
        'battery_health',
        'has_box',
        'has_cable',
        'imei',
        'serial_number',
        'supplier_cost',
        'suggested_price',
        'quantity',
        'available_quantity',
        'status',
        'notes',
        'received_at',
        'sold_at',
        'sale_id',
    ];

    protected function casts(): array
    {
        return [
            'supplier_cost' => 'decimal:2',
            'suggested_price' => 'decimal:2',
            'quantity' => 'integer',
            'available_quantity' => 'integer',
            'condition' => ProductCondition::class,
            'battery_health' => 'integer',
            'has_box' => 'boolean',
            'has_cable' => 'boolean',
            'status' => ConsignmentStatus::class,
            'received_at' => 'datetime',
            'sold_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ConsignmentBatch::class, 'batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ConsignmentStockMovement::class, 'consignment_item_id');
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(ConsignmentItemExchange::class, 'consignment_item_id')
            ->orderBy('exchanged_at');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Available);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Sold);
    }

    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', ConsignmentStatus::Returned);
    }

    public function scopeUsed(Builder $query): Builder
    {
        return $query->whereIn('condition', [ProductCondition::Used, ProductCondition::Refurbished]);
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('condition', ProductCondition::New);
    }

    public function scopeBySupplier(Builder $query, string $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('model', 'like', "%{$term}%")
              ->orWhere('imei', 'like', "%{$term}%")
              ->orWhere('color', 'like', "%{$term}%");
        });
    }

    public function getProductSignatureAttribute(): string
    {
        return implode('|', [
            strtolower(trim($this->name ?? '')),
            strtolower(trim($this->model ?? '')),
            strtolower(trim($this->storage ?? '')),
            strtolower(trim($this->color ?? '')),
            $this->supplier_id,
        ]);
    }

    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        if ($this->storage) {
            $parts[] = $this->storage;
        }
        if ($this->color) {
            $parts[] = $this->color;
        }

        return implode(' ', $parts);
    }

    public function getFormattedSupplierCostAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->supplier_cost, 2, ',', '.');
    }

    public function isAvailable(): bool
    {
        return $this->status === ConsignmentStatus::Available && $this->available_quantity > 0;
    }

    public function markAsSold(string $saleId): void
    {
        $this->update([
            'status' => ConsignmentStatus::Sold,
            'available_quantity' => 0,
            'sold_at' => now(),
            'sale_id' => $saleId,
        ]);
    }

    public function markAsAvailable(): void
    {
        $this->update([
            'status' => ConsignmentStatus::Available,
            'available_quantity' => $this->quantity,
            'sold_at' => null,
            'sale_id' => null,
        ]);
    }

    /**
     * Lista de IMEIs antigos (em ordem cronologica) deste item, derivada das trocas.
     *
     * @return array<int, string>
     */
    public function getImeiHistoryAttribute(): array
    {
        return $this->exchanges
            ->pluck('old_imei')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Lista de Seriais antigos (em ordem cronologica) deste item, derivada das trocas.
     *
     * @return array<int, string>
     */
    public function getSerialHistoryAttribute(): array
    {
        return $this->exchanges
            ->pluck('old_serial_number')
            ->filter()
            ->values()
            ->all();
    }

    public function hasBeenExchanged(): bool
    {
        return $this->exchanges()->exists();
    }

    /**
     * Snapshot enriquecido do item para ser persistido em SaleItem.product_snapshot.
     *
     * Inclui o IMEI/Serial atual e o historico de trocas para que, mesmo que o
     * ConsignmentStockItem seja alterado depois, a venda preserve a trilha.
     */
    public function toSaleSnapshot(): array
    {
        $exchanges = $this->exchanges->map(fn (ConsignmentItemExchange $e) => [
            'partner_name' => $e->partner_name,
            'old_imei' => $e->old_imei,
            'old_serial_number' => $e->old_serial_number,
            'old_name' => $e->old_name,
            'old_color' => $e->old_color,
            'old_storage' => $e->old_storage,
            'new_imei' => $e->new_imei,
            'new_serial_number' => $e->new_serial_number,
            'cost_adjustment' => (float) $e->cost_adjustment,
            'exchanged_at' => $e->exchanged_at?->toDateString(),
            'reason' => $e->reason,
        ])->values()->all();

        return [
            'consignment_item_id' => $this->id,
            'imei' => $this->imei,
            'serial_number' => $this->serial_number,
            'imei_history' => $this->imei_history,
            'serial_history' => $this->serial_history,
            'exchanges' => $exchanges,
            'has_been_exchanged' => !empty($exchanges),
        ];
    }
}
