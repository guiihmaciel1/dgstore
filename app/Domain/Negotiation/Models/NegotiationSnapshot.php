<?php

declare(strict_types=1);

namespace App\Domain\Negotiation\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Sale\Models\Sale;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationSnapshot extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'customer_id',
        'user_id',
        'product_description',
        'product_price',
        'product_cost',
        'trade_in_model',
        'trade_in_value',
        'trade_in_system_value',
        'down_payment',
        'card_balance',
        'commission_estimate',
        'message_text',
        'notes',
        'expires_at',
        'status',
        'sale_id',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'product_cost' => 'decimal:2',
            'trade_in_value' => 'decimal:2',
            'trade_in_system_value' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'card_balance' => 'decimal:2',
            'commission_estimate' => 'decimal:2',
            'expires_at' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeForCustomer(Builder $query, string $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }
}
