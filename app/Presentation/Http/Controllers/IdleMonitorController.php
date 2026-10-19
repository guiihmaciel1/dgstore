<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\TimeClock\Models\IdleLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IdleMonitorController extends Controller
{
    /**
     * Intern reports idle period ended (dismissed the popup).
     */
    public function dismiss(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idle_minutes' => 'required|integer|min:1',
            'started_at' => 'required|date',
        ]);

        $user = $request->user();
        $today = today()->toDateString();
        $cumulative = IdleLog::getDailyCumulative($user->id, $today) + $validated['idle_minutes'];

        $log = IdleLog::create([
            'user_id' => $user->id,
            'date' => $today,
            'idle_minutes' => $validated['idle_minutes'],
            'cumulative_minutes' => $cumulative,
            'dismissed' => true,
            'alert_sent' => $cumulative >= 30,
            'started_at' => $validated['started_at'],
            'ended_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'cumulative_minutes' => $cumulative,
            'alert_sent' => $log->alert_sent,
        ]);
    }

    /**
     * Check if idle alert threshold was reached (for live popup warning text).
     */
    public function alert(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today()->toDateString();
        $cumulative = IdleLog::getDailyCumulative($user->id, $today);

        return response()->json([
            'cumulative_minutes' => $cumulative,
            'alert_level' => match (true) {
                $cumulative >= 60 => 'critical',
                $cumulative >= 30 => 'warning',
                default => 'normal',
            },
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
