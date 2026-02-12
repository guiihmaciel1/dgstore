<?php

declare(strict_types=1);

namespace App\Domain\CashRegister\Models;

use App\Domain\CashRegister\Enums\CashEntryType;
use App\Domain\CashRegister\Enums\CashRegisterStatus;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CashRegister extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'opened_by',
        'closed_by',
        'status',
        'opening_balance',
        'closing_balance',
        'expected_balance',
        'difference',
        'opened_at',
        'closed_at',
        'closing_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => CashRegisterStatus::class,
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'expected_balance' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function openedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CashRegisterEntry::class);
    }

    // Scopes

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', CashRegisterStatus::Open);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('opened_at', today());
    }

    // Métodos

    public function isOpen(): bool
    {
        return $this->status === CashRegisterStatus::Open;
    }

    public function calculateExpectedBalance(): float
    {
        $inflow = $this->entries()
            ->whereIn('type', [CashEntryType::Sale, CashEntryType::Supply])
            ->sum('amount');

        $outflow = $this->entries()
            ->whereIn('type', [CashEntryType::Withdrawal, CashEntryType::TradeIn, CashEntryType::Expense])
            ->sum('amount');

        return (float) $this->opening_balance + $inflow - $outflow;
    }

    public function getTotalInflowAttribute(): float
    {
        return (float) $this->entries()
            ->whereIn('type', [CashEntryType::Sale, CashEntryType::Supply])
            ->sum('amount');
    }

    public function getTotalOutflowAttribute(): float
    {
        return (float) $this->entries()
            ->whereIn('type', [CashEntryType::Withdrawal, CashEntryType::TradeIn, CashEntryType::Expense])
            ->sum('amount');
    }

    public function getFormattedOpeningBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->opening_balance, 2, ',', '.');
    }

    public function getFormattedClosingBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->closing_balance, 2, ',', '.');
    }

    public function getFormattedExpectedBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->expected_balance, 2, ',', '.');
    }

    public function getFormattedDifferenceAttribute(): string
    {
        $diff = (float) $this->difference;
        $prefix = $diff >= 0 ? '+' : '';
        return $prefix . 'R$ ' . number_format($diff, 2, ',', '.');
    }
}
