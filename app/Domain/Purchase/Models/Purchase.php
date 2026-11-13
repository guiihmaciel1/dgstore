<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Models;

use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Compra para estoque: paga no ato, recebida depois (item a item).
 */
class Purchase extends Model
{
    use HasUlids;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'A receber',
        self::STATUS_PARTIAL => 'Parcial',
        self::STATUS_RECEIVED => 'Recebida',
        self::STATUS_CANCELLED => 'Cancelada',
    ];

    protected $fillable = [
        'number',
        'supplier_id',
        'supplier_name',
        'purchased_at',
        'expected_at',
        'status',
        'total',
        'account_id',
        'financial_transaction_id',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'date',
            'expected_at' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class)->orderBy('created_at')->orderBy('id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function supplierLabel(): string
    {
        return $this->supplier?->name ?? $this->supplier_name ?? 'Sem fornecedor';
    }

    public function isLate(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PARTIAL], true)
            && $this->expected_at !== null
            && $this->expected_at->lt(today());
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }
}
