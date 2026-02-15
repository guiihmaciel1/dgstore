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
        'cost_price',
        'supplier_origin',
        'freight_type',
        'freight_value',
        'freight_amount',
        'total_cost',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_snapshot' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'freight_value' => 'decimal:2',
            'freight_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
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
     * Custo unitário do produto no momento da venda.
     * Primeiro tenta o campo direto, depois faz fallback para o snapshot (vendas antigas).
     */
    public function getCostPriceValueAttribute(): float
    {
        if ($this->attributes['cost_price'] !== null) {
            return (float) $this->attributes['cost_price'];
        }

        return (float) ($this->product_snapshot['cost_price'] ?? 0);
    }

    /**
     * Retorna o label da origem do fornecedor
     */
    public function getSupplierOriginLabelAttribute(): string
    {
        return match ($this->supplier_origin) {
            'br' => 'Brasil',
            'py' => 'Paraguai',
            default => '-',
        };
    }

    /**
     * Calcula o valor do frete baseado no tipo
     */
    public function getFreightAmountCalculatedAttribute(): float
    {
        $costPrice = $this->cost_price_value;
        $freightValue = (float) ($this->attributes['freight_value'] ?? 0);

        return match ($this->freight_type) {
            'percentage' => $costPrice * ($freightValue / 100),
            'fixed' => $freightValue,
            default => 0,
        };
    }

    /**
     * Custo total real: custo + frete
     */
    public function getTotalCostValueAttribute(): float
    {
        if ($this->attributes['total_cost'] !== null && (float) $this->attributes['total_cost'] > 0) {
            return (float) $this->attributes['total_cost'];
        }

        return $this->cost_price_value + $this->freight_amount_calculated;
    }

    /**
     * Lucro do item: (preço venda - custo total) * quantidade
     */
    public function getItemProfitAttribute(): float
    {
        return ((float) $this->unit_price - $this->total_cost_value) * $this->quantity;
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->cost_price_value, 2, ',', '.');
    }

    public function getFormattedFreightAmountAttribute(): string
    {
        return 'R$ ' . number_format((float) ($this->attributes['freight_amount'] ?? 0), 2, ',', '.');
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return 'R$ ' . number_format($this->total_cost_value, 2, ',', '.');
    }

    public function getFormattedItemProfitAttribute(): string
    {
        return 'R$ ' . number_format($this->item_profit, 2, ',', '.');
    }
}
