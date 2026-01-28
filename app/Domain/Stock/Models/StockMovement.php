<?php

declare(strict_types=1);

namespace App\Domain\Stock\Models;

use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Domain\Stock\Enums\StockMovementType;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class StockMovement extends Model
{
    use HasFactory, HasUlids;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'reference_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (StockMovement $movement) {
            $movement->created_at = $movement->created_at ?? now();
        });
    }

    // Relacionamentos

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'reference_id');
    }

    // Scopes

    public function scopeInMovements(Builder $query): Builder
    {
        return $query->where('type', StockMovementType::In);
    }

    public function scopeOutMovements(Builder $query): Builder
    {
        return $query->where('type', StockMovementType::Out);
    }

    public function scopeAdjustments(Builder $query): Builder
    {
        return $query->where('type', StockMovementType::Adjustment);
    }

    public function scopeReturns(Builder $query): Builder
    {
        return $query->where('type', StockMovementType::Return);
    }

    public function scopeForProduct(Builder $query, string $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Métodos auxiliares

    public function isAddition(): bool
    {
        return $this->type->isAddition();
    }

    public function getSignedQuantityAttribute(): int
    {
        return $this->isAddition() ? $this->quantity : -$this->quantity;
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            StockMovementType::In => 'green',
            StockMovementType::Out => 'red',
            StockMovementType::Adjustment => 'yellow',
            StockMovementType::Return => 'blue',
        };
    }
}
