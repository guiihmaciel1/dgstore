<?php

declare(strict_types=1);

namespace App\Domain\Commission\Models;

use App\Domain\Sale\Models\Sale;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'sale_id',
        'sale_number',
        'sale_total',
        'commission_rate',
        'commission_type',
        'commission_amount',
        'status',
    ];

    protected $casts = [
        'sale_total' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_type' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
