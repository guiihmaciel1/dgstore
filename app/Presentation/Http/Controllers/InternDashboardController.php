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
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternDashboardController extends Controller
{
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

        $mySales = Sale::where('user_id', $user->id)
            ->whereBetween('sold_at', [$monthStart, $monthEnd])
            ->where('payment_status', '!=', PaymentStatus::Cancelled)
            ->get();

        $mySalesCount = $mySales->count();
        $mySalesTotal = (float) $mySales->sum('total');

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

        return view('intern.dashboard', compact(
            'user',
            'timeClockEntries',
            'nextPunchType',
            'punchMessages',
            'punchButtonLabels',
            'punchButtonColors',
            'mySalesCount',
            'mySalesTotal',
            'totalCommissions',
            'totalWithdrawn',
            'commissionBalance',
            'monthCommissions',
            'birthdayCustomers',
            'todayAppointments',
            'nextAppointment',
        ));
    }
}
