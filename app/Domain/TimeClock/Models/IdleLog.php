<?php

declare(strict_types=1);

namespace App\Domain\TimeClock\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdleLog extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'date',
        'idle_minutes',
        'cumulative_minutes',
        'dismissed',
        'alert_sent',
        'admin_read',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'idle_minutes' => 'integer',
            'cumulative_minutes' => 'integer',
            'dismissed' => 'boolean',
            'alert_sent' => 'boolean',
            'admin_read' => 'boolean',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('date', $date);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('date', today()->toDateString());
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('admin_read', false);
    }

    public static function getDailyCumulative(string $userId, string $date): int
    {
        return (int) static::forUser($userId)
            ->forDate($date)
            ->sum('idle_minutes');
    }
}
