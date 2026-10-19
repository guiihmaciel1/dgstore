<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Commission\Models\Commission;
use App\Domain\Commission\Models\CommissionWithdrawal;
use App\Domain\Customer\Models\Customer;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Models\Sale;
use App\Domain\Schedule\Models\Appointment;
use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class InternDashboardController extends Controller
{
    private const EXPECTED_HOURS_PER_DAY = 6;

    public function index(Request $request): View
    {
        $user = $request->user();

        $timeClockEntries = TimeClockEntry::getTodayEntries($user->id);
        $nextPunchType = TimeClockEntry::getNextExpectedType($user->id);

        $punchMessages = [
            TimeClockEntry::TYPE_CLOCK_IN => ['Bom dia, ' . $user->name . '!', 'Registre sua chegada'],
            TimeClockEntry::TYPE_LUNCH_OUT => ['Hora do almoço?', 'Registre sua saída para almoço'],
            TimeClockEntry::TYPE_LUNCH_IN => ['De volta!', 'Registre sua volta do almoço'],
            TimeClockEntry::TYPE_CLOCK_OUT => ['Encerrando o dia?', 'Registre sua saída'],
        ];

        $punchButtonLabels = [
            TimeClockEntry::TYPE_CLOCK_IN => 'Cheguei!',
            TimeClockEntry::TYPE_LUNCH_OUT => 'Saindo para almoço',
            TimeClockEntry::TYPE_LUNCH_IN => 'Voltei do almoço',
            TimeClockEntry::TYPE_CLOCK_OUT => 'Encerrando expediente',
        ];

        $punchButtonColors = [
            TimeClockEntry::TYPE_CLOCK_IN => '#059669',
            TimeClockEntry::TYPE_LUNCH_OUT => '#d97706',
            TimeClockEntry::TYPE_LUNCH_IN => '#2563eb',
            TimeClockEntry::TYPE_CLOCK_OUT => '#7c3aed',
        ];

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $mySales = Sale::where(function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $mySalesCount = $mySales->count();

        $totalCommissions = (float) Commission::forUser($user->id)->approved()->sum('commission_amount');
        $totalWithdrawn = (float) CommissionWithdrawal::forUser($user->id)->approved()->sum('amount');
        $commissionBalance = $totalCommissions - $totalWithdrawn;

        $monthCommissions = (float) Commission::forUser($user->id)
            ->approved()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        $birthdayCustomers = Customer::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->whereRaw('DAY(birth_date) >= ?', [now()->day])
            ->orderByRaw('DAY(birth_date) ASC')
            ->get();

        $todayAppointments = Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->orderBy('start_time')
            ->get();

        $nextAppointment = Appointment::forDate(today()->format('Y-m-d'))
            ->active()
            ->where('start_time', '>=', now()->format('H:i:s'))
            ->orderBy('start_time')
            ->first();

        $weekSummary = $this->buildWeekSummary($user->id);

        return view('intern.dashboard', compact(
            'user',
            'timeClockEntries',
            'nextPunchType',
            'punchMessages',
            'punchButtonLabels',
            'punchButtonColors',
            'mySalesCount',
            'totalCommissions',
            'totalWithdrawn',
            'commissionBalance',
            'monthCommissions',
            'birthdayCustomers',
            'todayAppointments',
            'nextAppointment',
            'weekSummary',
        ));
    }

    private function buildWeekSummary(string $userId): array
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

        $weekEntries = TimeClockEntry::forUser($userId)
            ->whereBetween('punched_at', [$startOfWeek, $endOfWeek])
            ->orderBy('punched_at')
            ->get();

        $byDate = $weekEntries->groupBy(fn ($e) => $e->punched_at->format('Y-m-d'));

        $days = [];
        $totalWorked = 0;
        $daysWorked = 0;

        foreach ($byDate as $dateStr => $dayEntries) {
            $sorted = $dayEntries->sortBy('punched_at');
            $minutes = $this->calcWorkedMinutes($sorted);

            if ($minutes > 0) {
                $totalWorked += $minutes;
                $daysWorked++;
            }

            $expected = self::EXPECTED_HOURS_PER_DAY * 60;
            $balance = $minutes - $expected;

            $days[$dateStr] = [
                'worked_minutes' => $minutes,
                'balance_minutes' => $balance,
            ];
        }

        $expectedTotal = $daysWorked * self::EXPECTED_HOURS_PER_DAY * 60;
        $totalBalance = $totalWorked - $expectedTotal;

        return [
            'days' => $days,
            'total_worked' => $totalWorked,
            'days_worked' => $daysWorked,
            'expected_total' => $expectedTotal,
            'total_balance' => $totalBalance,
            'avg_per_day' => $daysWorked > 0 ? (int) round($totalWorked / $daysWorked) : 0,
            'start' => $startOfWeek,
            'end' => $endOfWeek,
        ];
    }

    private function calcWorkedMinutes(Collection $entries): int
    {
        $clockIn = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_IN);
        if (!$clockIn) {
            return 0;
        }

        $clockOut = $entries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_OUT);
        if (!$clockOut) {
            return 0;
        }

        $totalMinutes = (int) $clockIn->punched_at->diffInMinutes($clockOut->punched_at);

        $lunchOut = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);
        $lunchIn = $entries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_IN);

        if ($lunchOut && $lunchIn) {
            $totalMinutes -= (int) $lunchOut->punched_at->diffInMinutes($lunchIn->punched_at);
        }

        return max(0, $totalMinutes);
    }
}
