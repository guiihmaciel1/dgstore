<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\TimeClock\Models\IdleLog;
use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Domain\WhatsApp\Services\UazapiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdleMonitorController extends Controller
{
    private const ADMIN_WHATSAPP = '5517992010606';

    /**
     * Check if monitoring is active (9:00-16:00 Mon-Fri, not during lunch).
     */
    private function isWithinWorkHours(string $userId): bool
    {
        $now = now();

        if ($now->isWeekend()) {
            return false;
        }

        $hour = (int) $now->format('H');
        if ($hour < 9 || $hour >= 16) {
            return false;
        }

        $todayEntries = TimeClockEntry::getTodayEntries($userId);
        $lunchOut = $todayEntries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);
        $lunchIn = $todayEntries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_IN);

        if ($lunchOut && !$lunchIn) {
            return false;
        }

        return true;
    }

    /**
     * Intern clicked "Sim, estou aqui" — registers idle period as dismissed.
     */
    public function dismiss(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idle_minutes' => 'required|integer|min:1',
            'started_at' => 'required|date',
        ]);

        $user = $request->user();
        $today = today()->toDateString();

        if (!$this->isWithinWorkHours($user->id)) {
            return response()->json(['ok' => true, 'skipped' => true]);
        }

        $cumulative = IdleLog::getDailyCumulative($user->id, $today) + $validated['idle_minutes'];

        IdleLog::create([
            'user_id' => $user->id,
            'date' => $today,
            'idle_minutes' => $validated['idle_minutes'],
            'cumulative_minutes' => $cumulative,
            'dismissed' => true,
            'alert_sent' => false,
            'started_at' => $validated['started_at'],
            'ended_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'cumulative_minutes' => $cumulative,
        ]);
    }

    /**
     * 5 min without response — registers idle period, sends WhatsApp alert.
     */
    public function alert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idle_minutes' => 'required|integer|min:1',
            'started_at' => 'required|date',
        ]);

        $user = $request->user();
        $today = today()->toDateString();

        if (!$this->isWithinWorkHours($user->id)) {
            return response()->json(['ok' => true, 'skipped' => true]);
        }

        $cumulative = IdleLog::getDailyCumulative($user->id, $today) + $validated['idle_minutes'];

        $log = IdleLog::create([
            'user_id' => $user->id,
            'date' => $today,
            'idle_minutes' => $validated['idle_minutes'],
            'cumulative_minutes' => $cumulative,
            'dismissed' => false,
            'alert_sent' => true,
            'started_at' => $validated['started_at'],
            'ended_at' => now(),
        ]);

        try {
            $name = $user->name;
            $startedAt = \Carbon\Carbon::parse($validated['started_at'])->format('H:i');
            $now = now()->format('H:i');

            $message = "⚠️ *{$name}* está inativa há *{$validated['idle_minutes']}min*\n"
                     . "Popup sem resposta desde {$startedAt}\n"
                     . "Acumulado hoje: *{$cumulative}min* de inatividade\n"
                     . "Horário: {$now}";

            app(UazapiService::class)->sendText(self::ADMIN_WHATSAPP, $message);
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'ok' => true,
            'cumulative_minutes' => $cumulative,
            'alert_sent' => true,
        ]);
    }

    /**
     * Admin: get unread idle notifications (badge count + list).
     */
    public function notifications(): JsonResponse
    {
        $unread = IdleLog::with('user:id,name')
            ->unread()
            ->where('alert_sent', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn (IdleLog $log) => [
                'id' => $log->id,
                'user_name' => $log->user->name,
                'idle_minutes' => $log->idle_minutes,
                'cumulative_minutes' => $log->cumulative_minutes,
                'date' => $log->date->format('d/m'),
                'started_at' => $log->started_at->format('H:i'),
                'ended_at' => $log->ended_at?->format('H:i'),
                'created_at' => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'count' => $unread->count(),
            'items' => $unread,
        ]);
    }

    /**
     * Admin: mark all idle notifications as read.
     */
    public function markRead(): JsonResponse
    {
        IdleLog::unread()
            ->where('alert_sent', true)
            ->update(['admin_read' => true]);

        return response()->json(['ok' => true]);
    }
}
