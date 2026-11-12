<?php

declare(strict_types=1);

namespace App\Domain\TimeClock\Services;

use App\Domain\TimeClock\Models\TimeClockEntry;
use Illuminate\Support\Collection;

/**
 * Cálculos de jornada a partir das batidas de ponto de um dia.
 */
class WorkedTimeCalculator
{
    public const EXPECTED_HOURS_PER_DAY = 6;

    /**
     * Minutos trabalhados no dia (descontando o almoço).
     * Com $isOngoing, um expediente ainda aberto conta até agora.
     */
    public static function workedMinutes(Collection $entries, bool $isOngoing = false): int
    {
        $clockIn = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_IN);
        if (! $clockIn) {
            return 0;
        }

        $clockOut = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_OUT);
        $lunchOut = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);
        $lunchIn = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_IN);

        $end = $clockOut ? $clockOut->punched_at : ($isOngoing ? now() : null);
        if (! $end) {
            return 0;
        }

        $totalMinutes = (int) $clockIn->punched_at->diffInMinutes($end);

        if ($lunchOut && $lunchIn) {
            $totalMinutes -= (int) $lunchOut->punched_at->diffInMinutes($lunchIn->punched_at);
        } elseif ($lunchOut && ! $lunchIn && $isOngoing) {
            $totalMinutes -= (int) $lunchOut->punched_at->diffInMinutes(now());
        }

        return max(0, $totalMinutes);
    }

    /**
     * Próxima batida esperada no dia, ou null se o expediente terminou.
     */
    public static function nextExpectedType(Collection $todayEntries): ?string
    {
        $doneTypes = $todayEntries->pluck('type')->all();

        foreach (TimeClockEntry::SEQUENCE as $type) {
            if (! in_array($type, $doneTypes, true)) {
                return $type;
            }
        }

        return null;
    }
}
