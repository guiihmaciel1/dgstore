<?php

declare(strict_types=1);

namespace App\Domain\Finance\Models;

use App\Domain\Finance\Enums\TransactionStatus;
use App\Domain\Finance\Enums\TransactionType;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinancialTransaction extends Model
{
    use HasUlids;

    protected $fillable = [
        'account_id',
        'category_id',
        'user_id',
        'type',
        'status',
        'amount',
        'description',
        'due_date',
        'paid_at',
        'payment_method',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // Relationships

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference');
    }

    // Scopes

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'overdue']);
    }

    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->unpaid()
            ->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year);
    }

    public function scopePaidThisMonth($query)
    {
        return $query->paid()
            ->whereNotNull('paid_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year);
    }

    public function scopeByDateRange($query, ?string $start, ?string $end)
    {
        if ($start) {
            $query->where('due_date', '>=', $start);
        }
        if ($end) {
            $query->where('due_date', '<=', $end);
        }
        return $query;
    }

    // Accessors

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === TransactionType::Income ? '+' : '-';
        return $prefix . ' R$ ' . number_format((float) $this->amount, 2, ',', '.');
    }

    public function getFormattedAmountPlainAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->amount, 2, ',', '.');
    }

    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date?->format('d/m/Y') ?? '';
    }

    public function getFormattedPaidAtAttribute(): ?string
    {
        return $this->paid_at?->format('d/m/Y H:i');
    }

    public function getIsOverdueCheckAttribute(): bool
    {
        return $this->status === TransactionStatus::Pending
            && $this->due_date?->isPast() === true;
    }
}
