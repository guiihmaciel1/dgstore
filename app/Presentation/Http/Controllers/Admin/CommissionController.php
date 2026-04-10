<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\Commission\Models\Commission;
use App\Domain\Commission\Models\CommissionWithdrawal;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $interns = User::where('role', UserRole::Intern)->where('active', true)->get();

        $selectedUserId = $request->input('user_id', $interns->first()?->id);
        $selectedUser = $selectedUserId ? User::find($selectedUserId) : null;

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $commissions = collect();
        $withdrawals = collect();
        $totalEarned = 0;
        $totalWithdrawn = 0;
        $balance = 0;
        $monthEarned = 0;

        if ($selectedUser) {
            $commissions = Commission::forUser($selectedUser->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->with('sale')
                ->orderByDesc('created_at')
                ->get();

            $withdrawals = CommissionWithdrawal::forUser($selectedUser->id)
                ->orderByDesc('created_at')
                ->get();

            $totalEarned = (float) Commission::forUser($selectedUser->id)
                ->approved()
                ->sum('commission_amount');

            $totalWithdrawn = (float) CommissionWithdrawal::forUser($selectedUser->id)
                ->approved()
                ->sum('amount');

            $balance = $totalEarned - $totalWithdrawn;

            $monthEarned = (float) $commissions->where('status', 'approved')->sum('commission_amount');
        }

        return view('admin.commissions.index', compact(
            'interns',
            'selectedUser',
            'commissions',
            'withdrawals',
            'totalEarned',
            'totalWithdrawn',
            'balance',
            'monthEarned',
            'month',
            'year',
        ));
    }

    public function updateRate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $user->update(['commission_rate' => $validated['commission_rate']]);

        return back()->with('success', "Taxa de comissão de {$user->name} atualizada para {$validated['commission_rate']}%.");
    }

    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        CommissionWithdrawal::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'reason' => $validated['reason'],
            'approved_by' => auth()->id(),
            'status' => 'approved',
        ]);

        return back()->with('success', 'Saque registrado com sucesso.');
    }

    public function approveWithdrawal(CommissionWithdrawal $withdrawal): RedirectResponse
    {
        $withdrawal->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Saque aprovado.');
    }

    public function rejectWithdrawal(CommissionWithdrawal $withdrawal): RedirectResponse
    {
        $withdrawal->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Saque rejeitado.');
    }
}
