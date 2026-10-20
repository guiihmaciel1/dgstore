<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\TimeClock\Models\TimeClockEntry;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TimeClockController extends Controller
{
    public function punch(Request $request): RedirectResponse
    {
        $user = $request->user();
        $nextType = TimeClockEntry::getNextExpectedType($user->id);

        if (!$nextType) {
            return back()->with('error', 'Todos os pontos do dia já foram registrados.');
        }

        if ($nextType === TimeClockEntry::TYPE_LUNCH_IN) {
            $lunchOut = TimeClockEntry::getTodayEntries($user->id)
                ->firstWhere('type', TimeClockEntry::TYPE_LUNCH_OUT);

            if ($lunchOut && $lunchOut->punched_at->diffInMinutes(now()) < 60) {
                $remaining = 60 - (int) $lunchOut->punched_at->diffInMinutes(now());
                return back()->with('error', "Almoço mínimo de 1 hora. Faltam {$remaining} minutos.");
            }
        }

        $alreadyExists = TimeClockEntry::getTodayEntries($user->id)
            ->contains('type', $nextType);

        if ($alreadyExists) {
            return back()->with('error', 'Este ponto já foi registrado.');
        }

        TimeClockEntry::create([
            'user_id' => $user->id,
            'type' => $nextType,
            'punched_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        $label = TimeClockEntry::LABELS[$nextType];

        return back()->with('success', "Ponto registrado: {$label} às " . now()->format('H:i'));
    }
}
