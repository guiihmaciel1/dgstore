<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Finance\Services\StoreCapitalService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CashSnapshotController extends Controller
{
    /**
     * Conferência de caixa (só admin geral): salva banco e espécie informados + estoque a custo de agora.
     */
    public function store(Request $request, StoreCapitalService $capital): RedirectResponse
    {
        abort_unless($request->user()?->isAdminGeral(), 403);

        $bank = StoreCapitalService::parseMoney($request->input('bank_amount'));
        $cash = StoreCapitalService::parseMoney($request->input('cash_amount'));

        $errors = [];
        if ($bank === null || $bank < 0 || $bank > 100_000_000) {
            $errors['bank_amount'] = 'Informe o saldo no banco (ex.: 12.345,67).';
        }
        if ($cash === null || $cash < 0 || $cash > 10_000_000) {
            $errors['cash_amount'] = 'Informe o valor em espécie (ex.: 1.500,00).';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors)->errorBag('cashCheck');
        }

        $capital->record($bank, $cash, $request->user());

        return back()->with('success', 'Caixa conferido.');
    }
}
