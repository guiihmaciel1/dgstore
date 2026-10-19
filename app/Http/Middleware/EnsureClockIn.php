<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\TimeClock\Models\TimeClockEntry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClockIn
{
    /**
     * Interns must register clock_in before accessing the system.
     * Only allows: intern dashboard, time-clock punch, profile, logout, and API routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isIntern()) {
            return $next($request);
        }

        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        if (!$this->isWorkHours()) {
            return $next($request);
        }

        $hasClockIn = TimeClockEntry::getTodayEntries($user->id)
            ->where('type', TimeClockEntry::TYPE_CLOCK_IN)
            ->isNotEmpty();

        if (!$hasClockIn) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Registre sua chegada primeiro.'], 403);
            }

            return redirect()
                ->route('intern.dashboard')
                ->with('error', '⏰ Registre sua chegada antes de acessar o sistema.');
        }

        return $next($request);
    }

    private function isExemptRoute(Request $request): bool
    {
        return $request->routeIs(
            'intern.dashboard',
            'intern.time-clock.punch',
            'profile.*',
            'logout',
            'keepalive',
            'idle-monitor.*',
            'idle-mode.*',
        );
    }

    private function isWorkHours(): bool
    {
        $now = now();

        if ($now->isWeekend()) {
            return false;
        }

        $hour = (int) $now->format('H');

        return $hour >= 8 && $hour < 17;
    }
}
