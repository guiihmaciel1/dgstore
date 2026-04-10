<?php

declare(strict_types=1);

namespace App\Domain\TimeClock\Models;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeClockEntry extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'type',
        'punched_at',
        'notes',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
    ];

    public const TYPE_CLOCK_IN = 'clock_in';
    public const TYPE_LUNCH_OUT = 'lunch_out';
    public const TYPE_LUNCH_IN = 'lunch_in';
    public const TYPE_CLOCK_OUT = 'clock_out';

    public const SEQUENCE = [
        self::TYPE_CLOCK_IN,
        self::TYPE_LUNCH_OUT,
        self::TYPE_LUNCH_IN,
        self::TYPE_CLOCK_OUT,
    ];

    public const LABELS = [
        self::TYPE_CLOCK_IN => 'Chegada',
        self::TYPE_LUNCH_OUT => 'Saída Almoço',
        self::TYPE_LUNCH_IN => 'Volta Almoço',
        self::TYPE_CLOCK_OUT => 'Saída',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabel(): string
    {
        return self::LABELS[$this->type] ?? $this->type;
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('punched_at', $date);
    }

    public static function getTodayEntries(string $userId): \Illuminate\Support\Collection
    {
        return static::forUser($userId)
            ->forDate(today()->toDateString())
            ->orderBy('punched_at')
            ->get();
    }

    public static function getNextExpectedType(string $userId): ?string
    {
        $todayEntries = static::getTodayEntries($userId);
        $doneTypes = $todayEntries->pluck('type')->toArray();

        foreach (self::SEQUENCE as $type) {
            if (!in_array($type, $doneTypes)) {
                return $type;
            }
        }

        return null;
    }
}
