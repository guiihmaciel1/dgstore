<?php

declare(strict_types=1);

namespace App\Domain\CashRegister\Models;

use App\Domain\CashRegister\Enums\CashEntryType;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterEntry extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'cash_register_id',
        'user_id',
        'type',
        'amount',
        'payment_method',
        'description',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => CashEntryType::class,
            'amount' => 'decimal:2',
        ];
    }

    // Relacionamentos

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors

    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type->isInflow() ? '+' : '-';
        return $prefix . ' R$ ' . number_format((float) $this->amount, 2, ',', '.');
    }
}
