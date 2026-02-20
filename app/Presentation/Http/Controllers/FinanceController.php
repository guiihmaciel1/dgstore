<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Finance\Models\FinancialCategory;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\Finance\Services\FinanceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    // ─── Dashboard ───

    public function index(): View
    {
        $this->financeService->markOverdueTransactions();
        $data = $this->financeService->getDashboardData();

        return view('finance.index', $data);
    }

    // ─── Contas a Pagar ───

    public function payables(Request $request): View
    {
        $this->financeService->markOverdueTransactions();

        $filters = [
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id'),
            'start_date' => $request->get('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->get('end_date', now()->endOfMonth()->format('Y-m-d')),
            'search' => $request->get('search'),
        ];

        $transactions = $this->financeService->getPayables(20, $filters);
        $summary = $this->financeService->getPayablesSummary($filters);
        $categories = $this->financeService->getCategoriesByType('expense');
        $accounts = $this->financeService->getAccounts();

        return view('finance.payables', compact('transactions', 'summary', 'categories', 'accounts', 'filters'));
    }

    // ─── Contas a Receber ───

    public function receivables(Request $request): View
    {
        $this->financeService->markOverdueTransactions();

        $filters = [
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id'),
            'start_date' => $request->get('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date' => $request->get('end_date', now()->endOfMonth()->format('Y-m-d')),
            'search' => $request->get('search'),
        ];

        $transactions = $this->financeService->getReceivables(20, $filters);
        $summary = $this->financeService->getReceivablesSummary($filters);
        $categories = $this->financeService->getCategoriesByType('income');
        $accounts = $this->financeService->getAccounts();

        return view('finance.receivables', compact('transactions', 'summary', 'categories', 'accounts', 'filters'));
    }

    // ─── Carteiras ───

    public function accounts(Request $request): View
    {
        $accounts = $this->financeService->getAccounts();
        $selectedAccountId = $request->get('account_id');
        $statement = collect();

        if ($selectedAccountId) {
            $statement = $this->financeService->getAccountStatement(
                $selectedAccountId,
                $request->get('start_date'),
                $request->get('end_date'),
            );
        }

        return view('finance.accounts', compact('accounts', 'selectedAccountId', 'statement'));
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:cash,bank,digital_wallet'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:7'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $this->financeService->createAccount([
            'name' => $request->name,
            'type' => $request->type,
            'initial_balance' => (float) ($request->initial_balance ?? 0),
            'color' => $request->color ?? '#111827',
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('finance.accounts')->with('success', 'Carteira criada com sucesso!');
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $request->validate([
            'from_account_id' => ['required', 'exists:financial_accounts,id'],
            'to_account_id' => ['required', 'exists:financial_accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $this->financeService->createTransfer(
                $request->from_account_id,
                $request->to_account_id,
                (float) $request->amount,
                auth()->id(),
                $request->description,
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('finance.accounts')->with('error', $e->getMessage());
        }

        return redirect()->route('finance.accounts')->with('success', 'Transferência realizada com sucesso!');
    }

    // ─── Categorias ───

    public function categories(): View
    {
        $categories = $this->financeService->getCategories();
        return view('finance.categories', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $this->financeService->createCategory([
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? '#6b7280',
            'is_system' => false,
        ]);

        return redirect()->route('finance.categories')->with('success', 'Categoria criada com sucesso!');
    }

    public function destroyCategory(FinancialCategory $category): RedirectResponse
    {
        if ($category->is_system) {
            return redirect()->route('finance.categories')->with('error', 'Categorias do sistema não podem ser removidas.');
        }

        $this->financeService->deleteCategory($category);

        return redirect()->route('finance.categories')->with('success', 'Categoria removida com sucesso!');
    }

    // ─── Transaction actions ───

    public function storeTransaction(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', 'exists:financial_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'account_id' => ['nullable', 'exists:financial_accounts,id'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'is_paid' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'installments' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        // Validar que a categoria pertence ao tipo da transação
        $category = FinancialCategory::find($request->category_id);
        if ($category && $category->type->value !== $request->type) {
            $route = $request->type === 'expense' ? 'finance.payables' : 'finance.receivables';
            return redirect()->route($route)->with('error', 'A categoria selecionada não corresponde ao tipo da transação.');
        }

        $isPaid = $request->boolean('is_paid');
        $installments = max(1, (int) ($request->installments ?? 1));
        $totalAmount = (float) $request->amount;

        if ($installments > 1) {
            // Criar múltiplas parcelas
            $installmentAmount = round($totalAmount / $installments, 2);
            $dueDate = \Carbon\Carbon::parse($request->due_date);
            $created = 0;

            for ($i = 1; $i <= $installments; $i++) {
                // Última parcela absorve a diferença de arredondamento
                $amount = ($i === $installments)
                    ? $totalAmount - ($installmentAmount * ($installments - 1))
                    : $installmentAmount;

                $this->financeService->createTransaction([
                    'type' => $request->type,
                    'category_id' => $request->category_id,
                    'user_id' => auth()->id(),
                    'amount' => $amount,
                    'description' => "{$request->description} ({$i}/{$installments})",
                    'due_date' => $dueDate->copy()->addMonths($i - 1)->toDateString(),
                    'account_id' => null,
                    'status' => 'pending',
                    'paid_at' => null,
                    'payment_method' => null,
                    'notes' => $request->notes,
                ]);
                $created++;
            }

            $route = $request->type === 'expense' ? 'finance.payables' : 'finance.receivables';
            $label = $request->type === 'expense' ? 'despesas' : 'receitas';

            return redirect()->route($route)->with('success', "{$created} {$label} parceladas registradas com sucesso!");
        }

        // Transação única
        $this->financeService->createTransaction([
            'type' => $request->type,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'amount' => $totalAmount,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'account_id' => $isPaid ? $request->account_id : null,
            'status' => $isPaid ? 'paid' : 'pending',
            'paid_at' => $isPaid ? now() : null,
            'payment_method' => $isPaid ? $request->payment_method : null,
            'notes' => $request->notes,
        ]);

        $route = $request->type === 'expense' ? 'finance.payables' : 'finance.receivables';
        $label = $request->type === 'expense' ? 'Despesa' : 'Receita';

        return redirect()->route($route)->with('success', "{$label} registrada com sucesso!");
    }

    public function payTransaction(Request $request, FinancialTransaction $transaction): RedirectResponse
    {
        $request->validate([
            'account_id' => ['required', 'exists:financial_accounts,id'],
            'payment_method' => ['nullable', 'string'],
        ]);

        $this->financeService->markAsPaid($transaction, $request->account_id, $request->payment_method);

        $route = $transaction->type->value === 'expense' ? 'finance.payables' : 'finance.receivables';

        return redirect()->route($route)->with('success', 'Transação marcada como paga!');
    }

    public function cancelTransaction(FinancialTransaction $transaction): RedirectResponse
    {
        $this->financeService->cancelTransaction($transaction);

        $route = $transaction->type->value === 'expense' ? 'finance.payables' : 'finance.receivables';

        return redirect()->route($route)->with('success', 'Transação cancelada!');
    }
}
