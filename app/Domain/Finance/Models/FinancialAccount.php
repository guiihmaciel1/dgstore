<?php

declare(strict_types=1);

namespace App\Domain\Finance\Models;

use App\Domain\Finance\Enums\AccountType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'type',
        'initial_balance',
        'current_balance',
        'color',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'type' => AccountType::class,
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'account_id');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(AccountTransfer::class, 'from_account_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(AccountTransfer::class, 'to_account_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accessors

    public function getFormattedBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->current_balance, 2, ',', '.');
    }

    public function getFormattedInitialBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->initial_balance, 2, ',', '.');
    }

    // Methods

    public function addBalance(float $amount): void
    {
        $this->increment('current_balance', $amount);
    }

    public function subtractBalance(float $amount): void
    {
        $this->decrement('current_balance', $amount);
    }

    public function recalculateBalance(): void
    {
        $income = (float) $this->transactions()
            ->where('type', 'income')
            ->where('status', 'paid')
            ->sum('amount');

        $expense = (float) $this->transactions()
            ->where('type', 'expense')
            ->where('status', 'paid')
            ->sum('amount');

        $transfersIn = (float) $this->incomingTransfers()->sum('amount');
        $transfersOut = (float) $this->outgoingTransfers()->sum('amount');

        $this->update([
            'current_balance' => $this->initial_balance + $income - $expense + $transfersIn - $transfersOut,
        ]);
    }
}
