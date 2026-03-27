<?php

declare(strict_types=1);

namespace App\Domain\Schedule\Models;

use App\Domain\Customer\Models\Customer;
use App\Domain\Schedule\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    use HasUlids, SoftDeletes;

    public const ATTENDANTS = [
        'danilo'    => 'Danilo',
        'guilherme' => 'Guilherme',
    ];

    public const MIN_HOUR = 8;
    public const MAX_HOUR = 23;

    public const DURATION_OPTIONS = [
        30  => '30 min',
        60  => '1 hora',
        90  => '1h 30min',
        120 => '2 horas',
        150 => '2h 30min',
        180 => '3 horas',
    ];

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'attendant',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'service_description',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date'             => 'date',
            'duration_minutes' => 'integer',
            'status'           => AppointmentStatus::class,
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
            'deleted_at'       => 'datetime',
        ];
    }

    // Relationships

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Scopes

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('date', $date);
    }

    public function scopeForAttendant(Builder $query, string $attendant): Builder
    {
        return $query->where('attendant', $attendant);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            AppointmentStatus::Cancelled->value,
        ]);
    }

    // Helpers

    public static function hasConflict(
        string $attendant,
        string $date,
        string $startTime,
        string $endTime,
        ?string $excludeId = null,
    ): bool {
        return self::query()
            ->forAttendant($attendant)
            ->forDate($date)
            ->active()
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public static function getAvailableSlots(string $attendant, string $date, int $durationMinutes): array
    {
        $existing = self::query()
            ->forAttendant($attendant)
            ->forDate($date)
            ->active()
            ->orderBy('start_time')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $current = Carbon::createFromTime(self::MIN_HOUR, 0);
        $dayEnd  = Carbon::createFromTime(self::MAX_HOUR, 0);

        while ($current->copy()->addMinutes($durationMinutes)->lte($dayEnd)) {
            $slotStart = $current->format('H:i');
            $slotEnd   = $current->copy()->addMinutes($durationMinutes)->format('H:i');

            $conflict = $existing->contains(function ($appt) use ($slotStart, $slotEnd) {
                return $appt->start_time < $slotEnd && $appt->end_time > $slotStart;
            });

            if (!$conflict) {
                $slots[] = [
                    'start' => $slotStart,
                    'end'   => $slotEnd,
                    'label' => $slotStart . ' - ' . $slotEnd,
                ];
            }

            $current->addMinutes(30);
        }

        return $slots;
    }

    // Accessors

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getFormattedStartTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5);
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return substr($this->end_time, 0, 5);
    }

    public function getAttendantNameAttribute(): string
    {
        return self::ATTENDANTS[$this->attendant] ?? $this->attendant;
    }

    public function getFormattedDurationAttribute(): string
    {
        return self::DURATION_OPTIONS[$this->duration_minutes] ?? $this->duration_minutes . ' min';
    }
}
