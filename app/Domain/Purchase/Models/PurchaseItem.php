<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Models;

use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Item de uma compra. Aparelhos com IMEI viram uma linha por unidade
 * (quantity = 1); os demais produtos ficam numa linha com a quantidade.
 */
class PurchaseItem extends Model
{
    use HasUlids;

    public const STATUS_PENDING = 'pending';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    /** Categorias cadastradas uma unidade por vez (têm IMEI/serial). */
    public const UNIT_CATEGORIES = ['smartphone', 'tablet', 'notebook', 'smartwatch'];

    protected $fillable = [
        'purchase_id',
        'category',
        'condition',
        'model',
        'storage',
        'color',
        'unit_cost',
        'quantity',
        'status',
        'product_id',
        'received_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'quantity' => 'integer',
            'received_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function totalCost(): float
    {
        return (float) $this->unit_cost * $this->quantity;
    }

    public function categoryLabel(): string
    {
        return ProductCategory::tryFrom($this->category)?->label() ?? ucfirst($this->category);
    }

    public function conditionLabel(): string
    {
        return ProductCondition::tryFrom($this->condition)?->label() ?? ucfirst($this->condition);
    }

    /**
     * Nome no padrão do estoque: "iPhone 17 Pro Max 256GB Laranja".
     */
    public function displayName(): string
    {
        $prefix = $this->category === 'smartphone' && ! str_starts_with(mb_strtolower($this->model), 'iphone') ? 'iPhone ' : '';

        return trim($prefix . $this->model . ' ' . ($this->storage ?? '') . ' ' . ($this->color ?? ''));
    }
}
