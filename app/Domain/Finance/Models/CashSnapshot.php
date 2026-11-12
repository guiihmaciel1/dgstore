<?php

declare(strict_types=1);

namespace App\Domain\Finance\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Conferência do caixa real: banco e espécie informados por alguém da loja,
 * mais o estoque a custo (novos, seminovos, outros) calculado na hora.
 */
class CashSnapshot extends Model
{
    use HasUlids;

    protected $fillable = [
        'bank_amount',
        'cash_amount',
        'stock_new_cost',
        'stock_used_cost',
        'stock_other_cost',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'bank_amount' => 'decimal:2',
            'cash_amount' => 'decimal:2',
            'stock_new_cost' => 'decimal:2',
            'stock_used_cost' => 'decimal:2',
            'stock_other_cost' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Caixa real: banco + espécie + iPhones novos + iPhones seminovos.
     */
    public function realTotal(): float
    {
        return (float) $this->bank_amount + (float) $this->cash_amount
            + (float) $this->stock_new_cost + (float) $this->stock_used_cost;
    }
}
