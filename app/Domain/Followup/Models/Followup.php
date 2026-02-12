<?php

declare(strict_types=1);

namespace App\Domain\Followup\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Followup\Enums\FollowupStatus;
use App\Domain\Followup\Enums\FollowupType;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'customer_id',
        'type',
        'title',
        'description',
        'phone',
        'due_date',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => FollowupType::class,
            'status' => FollowupStatus::class,
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relacionamentos

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    // Scopes

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', FollowupStatus::Pending);
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', FollowupStatus::Done);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('status', FollowupStatus::Pending)
            ->whereDate('due_date', today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', FollowupStatus::Pending)
            ->whereDate('due_date', '<', today());
    }

    public function scopeDueOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('due_date', $date);
    }

    // Helpers

    public function isPending(): bool
    {
        return $this->status === FollowupStatus::Pending;
    }

    public function isOverdue(): bool
    {
        return $this->isPending() && $this->due_date->lt(today());
    }

    public function isToday(): bool
    {
        return $this->isPending() && $this->due_date->isToday();
    }

    public function markAsDone(): void
    {
        $this->update([
            'status' => FollowupStatus::Done,
            'completed_at' => now(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => FollowupStatus::Cancelled,
        ]);
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $this->phone);

        return "https://wa.me/{$phone}";
    }
}
