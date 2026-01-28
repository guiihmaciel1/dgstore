<?php

declare(strict_types=1);

namespace App\Domain\Reservation\Models;

use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationPayment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'reservation_id',
        'user_id',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
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
}
