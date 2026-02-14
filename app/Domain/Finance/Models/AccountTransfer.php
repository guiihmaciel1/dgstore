<?php

declare(strict_types=1);

namespace App\Domain\Finance\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransfer extends Model
{
    use HasUlids;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'user_id',
        'amount',
        'description',
        'transferred_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transferred_at' => 'datetime',
    ];

    // Relationships

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'to_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->amount, 2, ',', '.');
    }

    public function getFormattedTransferredAtAttribute(): string
    {
        return $this->transferred_at->format('d/m/Y H:i');
    }
}
