<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeClockAdminController extends Controller
{
    public function index(Request $request): View
    {
        $interns = User::where('role', UserRole::Intern)->where('active', true)->get();
        $selectedUserId = $request->input('user_id');
        $date = $request->input('date', today()->format('Y-m-d'));

        $query = TimeClockEntry::with('user')
            ->orderByDesc('punched_at');

        if ($selectedUserId) {
            $query->forUser($selectedUserId);
        } else {
            $internIds = $interns->pluck('id');
            $query->whereIn('user_id', $internIds);
        }

        $entries = $query->get();

        $groupedByDate = $entries->groupBy(fn ($e) => $e->punched_at->format('Y-m-d'));

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

            $todayStatus[] = [
                'user' => $intern,
                'entries' => $todayEntries,
                'next_type' => $nextType,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'clock_in_time' => $clockIn?->punched_at?->format('H:i'),
            ];
        }

        return view('admin.time-clock.index', compact(
            'interns',
            'selectedUserId',
            'groupedByDate',
            'todayStatus',
            'date',
        ));
    }
}
