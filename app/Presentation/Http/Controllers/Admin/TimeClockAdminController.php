<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\TimeClock\Models\IdleLog;
use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TimeClockAdminController extends Controller
{
    private const EXPECTED_HOURS_PER_DAY = 8;

    public function index(Request $request): View
    {
        $interns = User::where('role', UserRole::Intern)->where('active', true)->get();
        $selectedUserId = $request->input('user_id');

        $query = TimeClockEntry::with('user')
            ->orderByDesc('punched_at');

        if ($selectedUserId) {
            $query->forUser($selectedUserId);
        } else {
            $query->whereIn('user_id', $interns->pluck('id'));
        }

        $entries = $query->get();
        $groupedByDate = $entries->groupBy(fn ($e) => $e->punched_at->format('Y-m-d'));

        $todayStatus = $this->buildTodayStatus($interns);
        $dailyHours = $this->buildDailyHours($groupedByDate);
        $weeklySummary = $this->buildWeeklySummary($interns, $selectedUserId);
        $idleSummary = $this->buildIdleSummary($interns, $selectedUserId);

        return view('admin.time-clock.index', compact(
            'interns',
            'selectedUserId',
            'groupedByDate',
            'todayStatus',
            'dailyHours',
            'weeklySummary',
            'idleSummary',
        ));
    }

    private function buildTodayStatus(Collection $interns): array
    {
        $todayStatus = [];

        foreach ($interns as $intern) {
            $todayEntries = TimeClockEntry::getTodayEntries($intern->id);
            $nextType = TimeClockEntry::getNextExpectedType($intern->id);

            $statusLabel = match (true) {
                $todayEntries->isEmpty() => 'Ainda não chegou',
                $nextType === TimeClockEntry::TYPE_LUNCH_OUT => 'Trabalhando',
                $nextType === TimeClockEntry::TYPE_LUNCH_IN => 'Almoçando',
                $nextType === TimeClockEntry::TYPE_CLOCK_OUT => 'Trabalhando (pós-almoço)',
                $nextType === null => 'Expediente encerrado',
                default => 'Indefinido',
            };

            $statusColor = match (true) {
                $todayEntries->isEmpty() => '#6b7280',
                $nextType === null => '#059669',
                $nextType === TimeClockEntry::TYPE_LUNCH_IN => '#d97706',
                default => '#2563eb',
            };

            $clockIn = $todayEntries->firstWhere('type', 'clock_in');
            $workedMinutes = $this->calcWorkedMinutes($todayEntries, isOngoing: $nextType !== null && $todayEntries->isNotEmpty());

            $todayStatus[] = [
                'user' => $intern,
                'entries' => $todayEntries,
                'next_type' => $nextType,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'clock_in_time' => $clockIn?->punched_at?->format('H:i'),
                'worked_minutes' => $workedMinutes,
            ];
        }

        return $todayStatus;
    }

    private function buildDailyHours(Collection $groupedByDate): array
    {
        $dailyHours = [];

        foreach ($groupedByDate as $dateStr => $dayEntries) {
            $byUser = $dayEntries->groupBy('user_id');

            foreach ($byUser as $userId => $userEntries) {
                $sorted = $userEntries->sortBy('punched_at');
                $minutes = $this->calcWorkedMinutes($sorted);
                $expected = self::EXPECTED_HOURS_PER_DAY * 60;
                $balance = $minutes - $expected;

                $dailyHours[$dateStr][$userId] = [
                    'worked_minutes' => $minutes,
                    'balance_minutes' => $balance,
                ];
            }
        }

        return $dailyHours;
    }

    private function buildWeeklySummary(Collection $interns, ?string $selectedUserId): array
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

        $targetUsers = $selectedUserId
            ? $interns->where('id', $selectedUserId)
            : $interns;

        $summary = [];

        foreach ($targetUsers as $intern) {
            $weekEntries = TimeClockEntry::forUser($intern->id)
                ->whereBetween('punched_at', [$startOfWeek, $endOfWeek])
                ->orderBy('punched_at')
                ->get();

            $byDate = $weekEntries->groupBy(fn ($e) => $e->punched_at->format('Y-m-d'));
            $totalWorked = 0;
            $daysWorked = 0;

            foreach ($byDate as $dayEntries) {
                $minutes = $this->calcWorkedMinutes($dayEntries->sortBy('punched_at'));
                if ($minutes > 0) {
                    $totalWorked += $minutes;
                    $daysWorked++;
                }
            }

            $weekdaysPassed = min((int) now()->diffInDays($startOfWeek), 6) + 1;
            if (now()->isSunday()) {
                $weekdaysPassed = 6;
            }
            $workdaysPassed = min($weekdaysPassed, 6);
            $expectedMinutes = $daysWorked * self::EXPECTED_HOURS_PER_DAY * 60;
            $balance = $totalWorked - $expectedMinutes;

            $summary[] = [
                'user' => $intern,
                'total_worked_minutes' => $totalWorked,
                'days_worked' => $daysWorked,
                'expected_minutes' => $expectedMinutes,
                'balance_minutes' => $balance,
                'avg_minutes_per_day' => $daysWorked > 0 ? (int) round($totalWorked / $daysWorked) : 0,
            ];
        }

        return $summary;
    }

    private function buildIdleSummary(Collection $interns, ?string $selectedUserId): array
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

        $targetUsers = $selectedUserId
            ? $interns->where('id', $selectedUserId)
            : $interns;

        $summary = [];

        foreach ($targetUsers as $intern) {
            $todayLogs = IdleLog::forUser($intern->id)
                ->forDate(today()->toDateString())
                ->orderByDesc('started_at')
                ->get();

            $todayTotal = $todayLogs->sum('idle_minutes');

            $weekTotal = (int) IdleLog::forUser($intern->id)
                ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->sum('idle_minutes');

            $weekCount = (int) IdleLog::forUser($intern->id)
                ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
                ->count();

            $summary[] = [
                'user' => $intern,
                'today_idle_minutes' => $todayTotal,
                'today_logs' => $todayLogs,
                'week_idle_minutes' => $weekTotal,
                'week_idle_count' => $weekCount,
            ];
        }

        return $summary;
    }

    private function calcWorkedMinutes(Collection $entries, bool $isOngoing = false): int
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
            $lunchMinutes = (int) $lunchOut->punched_at->diffInMinutes($lunchIn->punched_at);
            $totalMinutes -= $lunchMinutes;
        } elseif ($lunchOut && ! $lunchIn && $isOngoing) {
            $lunchMinutes = (int) $lunchOut->punched_at->diffInMinutes(now());
            $totalMinutes -= $lunchMinutes;
        }

        return max(0, $totalMinutes);
    }
}
