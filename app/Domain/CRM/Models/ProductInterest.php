<?php

declare(strict_types=1);

namespace App\Domain\CRM\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInterest extends Model
{
    use HasUlids;

    protected $fillable = [
        'deal_id',
        'customer_id',
        'model',
        'storage',
        'color',
        'condition',
        'max_budget',
        'notified',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'max_budget'  => 'decimal:2',
            'notified'    => 'boolean',
            'notified_at' => 'datetime',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('notified', false);
    }

    public function matchingProducts(): Builder
    {
        return Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->where('model', 'LIKE', '%' . $this->model . '%')
            ->when($this->storage, fn (Builder $q) => $q->where('storage', $this->storage))
            ->when($this->color, fn (Builder $q) => $q->where('color', $this->color))
            ->when($this->condition, function (Builder $q) {
                $conditionMap = ['novo' => 'new', 'seminovo' => 'used'];
                $mapped = $conditionMap[$this->condition] ?? $this->condition;
                return $q->where('condition', $mapped);
            });
    }

    public function hasMatchInStock(): bool
    {
        return $this->matchingProducts()->exists();
    }

    public function markAsNotified(): void
    {
        $this->update([
            'notified'    => true,
            'notified_at' => now(),
        ]);
    }
}
