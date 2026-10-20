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

        $clockOut = $todayEntries->firstWhere('type', TimeClockEntry::TYPE_CLOCK_OUT);
        if ($clockOut) {
            return false;
        }

        $lunchOut = $todayEntries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);
        $lunchIn = $todayEntries->firstWhere('type', TimeClockEntry::TYPE_LUNCH_IN);

        if ($lunchOut && !$lunchIn) {
            return false;
        }

        return true;
    }

    /**
     * Check if an alert was already sent for this exact idle period.
     */
    private function alreadyLoggedForPeriod(string $userId, string $startedAt): bool
    {
        return IdleLog::forUser($userId)
            ->where('started_at', $startedAt)
            ->exists();
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

        // If an alert was already sent for this period, just mark it as dismissed
        // without adding to the cumulative (avoids double-counting)
        $existingAlert = IdleLog::forUser($user->id)
            ->where('started_at', $validated['started_at'])
            ->where('alert_sent', true)
            ->first();

        if ($existingAlert) {
            $existingAlert->update(['dismissed' => true, 'ended_at' => now()->utc()]);

            return response()->json([
                'ok' => true,
                'cumulative_minutes' => $existingAlert->cumulative_minutes,
            ]);
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
            'ended_at' => now()->utc(),
        ]);

        return response()->json([
            'ok' => true,
            'cumulative_minutes' => $cumulative,
        ]);
    }

    /**
     * 5 min without response — registers idle period, sends WhatsApp alert.
     * Repeated alerts for the same period update the existing record.
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

        // If we already logged this idle period, just update the duration
        $existing = IdleLog::forUser($user->id)
            ->where('started_at', $validated['started_at'])
            ->where('alert_sent', true)
            ->first();

        if ($existing) {
            $diff = $validated['idle_minutes'] - $existing->idle_minutes;
            $newCumulative = $existing->cumulative_minutes + max(0, $diff);

            $existing->update([
                'idle_minutes' => $validated['idle_minutes'],
                'cumulative_minutes' => $newCumulative,
                'ended_at' => now()->utc(),
            ]);

            return response()->json([
                'ok' => true,
                'cumulative_minutes' => $newCumulative,
                'alert_sent' => true,
            ]);
        }

        $cumulative = IdleLog::getDailyCumulative($user->id, $today) + $validated['idle_minutes'];

        IdleLog::create([
            'user_id' => $user->id,
            'date' => $today,
            'idle_minutes' => $validated['idle_minutes'],
            'cumulative_minutes' => $cumulative,
            'dismissed' => false,
            'alert_sent' => true,
            'started_at' => $validated['started_at'],
            'ended_at' => now()->utc(),
        ]);

        try {
            $startedAt = \Carbon\Carbon::parse($validated['started_at'])->setTimezone('America/Sao_Paulo')->format('H:i');

            $message = "⚠️ *Sistema* está inativo há *{$validated['idle_minutes']}min*\n"
                     . "Popup sem resposta desde {$startedAt}\n"
                     . "Acumulado hoje: *{$cumulative}min* de inatividade";

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
                'started_at' => $log->started_at->setTimezone('America/Sao_Paulo')->format('H:i'),
                'ended_at' => $log->ended_at?->setTimezone('America/Sao_Paulo')->format('H:i'),
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
