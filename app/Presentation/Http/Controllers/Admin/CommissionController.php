<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\Commission\Models\Commission;
use App\Domain\Commission\Models\CommissionWithdrawal;
use App\Domain\Finance\Services\FinanceService;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(private FinanceService $financeService) {}

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
        $type = $request->input('commission_type', 'percentage');

        $rules = [
            'commission_type' => ['required', 'in:percentage,fixed'],
            'commission_rate' => ['required', 'numeric', 'min:0'],
        ];

        if ($type === 'percentage') {
            $rules['commission_rate'][] = 'max:100';
        }

        $validated = $request->validate($rules);

        $user->update([
            'commission_rate' => $validated['commission_rate'],
            'commission_type' => $validated['commission_type'],
        ]);

        $label = $validated['commission_type'] === 'fixed'
            ? "R$ " . number_format((float) $validated['commission_rate'], 2, ',', '.') . " por venda"
            : "{$validated['commission_rate']}%";

        return back()->with('success', "Taxa de comissão de {$user->name} atualizada para {$label}.");
    }

    public function storeManual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'           => ['required', 'exists:users,id'],
            'commission_amount' => ['required', 'numeric', 'min:0.01'],
            'description'       => ['required', 'string', 'max:500'],
            'date'              => ['required', 'date'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        Commission::create([
            'user_id'           => $user->id,
            'sale_id'           => null,
            'sale_number'       => null,
            'sale_total'        => null,
            'commission_rate'   => 0,
            'commission_type'   => 'fixed',
            'commission_amount' => $validated['commission_amount'],
            'description'       => $validated['description'],
            'is_manual'         => true,
            'status'            => 'approved',
        ]);

        return back()->with('success', 'Comissão manual lançada com sucesso.');
    }

    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'date'    => ['required', 'date'],
            'reason'  => ['required', 'string', 'max:500'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $withdrawal = CommissionWithdrawal::create([
            'user_id'     => $validated['user_id'],
            'amount'      => $validated['amount'],
            'date'        => $validated['date'],
            'reason'      => $validated['reason'],
            'approved_by' => auth()->id(),
            'status'      => 'approved',
        ]);

        $this->financeService->registerCommissionPayment(
            userId: auth()->id(),
            amount: (float) $validated['amount'],
            internName: $user->name,
            withdrawalId: $withdrawal->id,
            date: \Carbon\Carbon::parse($validated['date']),
        );

        return back()->with('success', 'Pagamento de comissão registrado e lançado no financeiro.');
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
