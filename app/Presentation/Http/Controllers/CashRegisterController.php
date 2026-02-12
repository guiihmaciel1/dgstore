<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\CashRegister\Enums\CashEntryType;
use App\Domain\CashRegister\Models\CashRegister;
use App\Domain\CashRegister\Services\CashRegisterService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function __construct(
        private readonly CashRegisterService $service
    ) {}

    public function index(): View
    {
        $openRegister = $this->service->getOpenRegister();
        $summary = $openRegister ? $this->service->getSummary($openRegister) : null;
        $history = $this->service->getHistory(10);

        return view('cash-register.index', [
            'openRegister' => $openRegister,
            'summary' => $summary,
            'history' => $history,
            'entryTypes' => CashEntryType::cases(),
        ]);
    }

    public function open(Request $request): RedirectResponse
    {
        $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->service->open(auth()->id(), (float) $request->opening_balance);
            return redirect()->route('cash-register.index')->with('success', 'Caixa aberto com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function close(Request $request, CashRegister $register): RedirectResponse
    {
        $request->validate([
            'closing_balance' => ['required', 'numeric', 'min:0'],
            'closing_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->service->close(
                $register,
                auth()->id(),
                (float) $request->closing_balance,
                $request->closing_notes
            );
            return redirect()->route('cash-register.index')->with('success', 'Caixa fechado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function addEntry(Request $request, CashRegister $register): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:withdrawal,supply,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->service->addEntry(
                $register,
                auth()->id(),
                CashEntryType::from($request->type),
                (float) $request->amount,
                $request->description
            );

            $label = CashEntryType::from($request->type)->label();
            return redirect()->route('cash-register.index')->with('success', "{$label} registrada com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
