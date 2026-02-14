<?php

declare(strict_types=1);

namespace App\Domain\Sale\Models;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (SaleItem $item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::updating(function (SaleItem $item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });
    }

    // Relacionamentos

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    // Métodos auxiliares

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->unit_price, 2, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->subtotal, 2, ',', '.');
    }

    /**
     * Retorna o nome do produto do snapshot
     */
    public function getProductNameAttribute(): string
    {
        return $this->product_snapshot['name'] ?? $this->product?->name ?? 'Produto removido';
    }

    /**
     * Retorna o SKU do produto do snapshot
     */
    public function getProductSkuAttribute(): string
    {
        return $this->product_snapshot['sku'] ?? $this->product?->sku ?? '-';
    }

    /**
     * Custo unitário do produto no momento da venda
     */
    public function getCostPriceAttribute(): float
    {
        return (float) ($this->product_snapshot['cost_price'] ?? 0);
    }

    /**
     * Lucro do item: (preço venda - custo) * quantidade
     */
    public function getItemProfitAttribute(): float
    {
        return ((float) $this->unit_price - $this->cost_price) * $this->quantity;
    }
}
