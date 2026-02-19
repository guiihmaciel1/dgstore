<?php

declare(strict_types=1);

namespace App\Domain\Perfumes\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfumeReservationPayment extends Model
{
    use HasUlids;

    protected $fillable = [
        'perfume_reservation_id',
        'user_id',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'  => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(PerfumeReservation::class, 'perfume_reservation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'pix'  => 'PIX',
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            default => $this->payment_method,
        };
    }
}
