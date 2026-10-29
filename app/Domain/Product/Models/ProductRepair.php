<?php

declare(strict_types=1);

namespace App\Domain\Product\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRepair extends Model
{
    use HasUlids;

    protected $fillable = [
        'product_id',
        'sent_by',
        'returned_by',
        'provider',
        'reason',
        'cost',
        'notes',
        'sent_at',
        'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'sent_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('returned_at');
    }

    public function isOpen(): bool
    {
        return $this->returned_at === null;
    }

    public function getDaysOutAttribute(): int
    {
        return (int) $this->sent_at->diffInDays($this->returned_at ?? now());
    }
}
