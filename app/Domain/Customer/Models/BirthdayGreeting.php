<?php

declare(strict_types=1);

namespace App\Domain\Customer\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mensagem de aniversário enviada (ou tentada) para um cliente em um ano.
 */
class BirthdayGreeting extends Model
{
    use HasUlids;

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'customer_id',
        'year',
        'phone',
        'status',
        'sent_by',
        'wa_message_id',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }
}
