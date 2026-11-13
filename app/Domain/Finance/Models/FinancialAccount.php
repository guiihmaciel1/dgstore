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

    /** Cor da carteira é clara? Usado para escolher ícone escuro ou claro sobre ela. */
    public function isLightColor(): bool
    {
        $hex = ltrim((string) $this->color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (! ctype_xdigit($hex) || strlen($hex) !== 6) {
            return false;
        }
        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160;
    }

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
