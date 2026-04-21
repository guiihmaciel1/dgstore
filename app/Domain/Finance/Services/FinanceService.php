<?php

declare(strict_types=1);

namespace App\Domain\Finance\Services;

use App\Domain\Finance\Enums\TransactionStatus;
use App\Domain\Finance\Enums\TransactionType;
use App\Domain\Finance\Models\AccountTransfer;
use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Finance\Models\FinancialCategory;
use App\Domain\Finance\Models\FinancialTransaction;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    // ─── Dashboard ───

    public function getDashboardData(?\Carbon\Carbon $referenceDate = null): array
    {
        $ref = $referenceDate ?? now();
        $monthStart = $ref->copy()->startOfMonth();
        $monthEnd = $ref->copy()->endOfMonth();
        $isCurrentMonth = $ref->isSameMonth(now());

        $accounts = FinancialAccount::active()->orderByDesc('is_default')->orderBy('name')->get();
        $totalBalance = (float) $accounts->sum('current_balance');

        $monthIncome = (float) FinancialTransaction::income()
            ->paid()
            ->whereNotNull('paid_at')
            ->whereNotNull('account_id')
            ->whereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->sum('amount');

        $monthExpensePaid = (float) FinancialTransaction::expense()
            ->paid()
            ->whereNotNull('paid_at')
            ->whereNotNull('account_id')
            ->whereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->sum('amount');

        $monthProfit = $monthIncome - $monthExpensePaid;

        $monthExpensePending = (float) FinancialTransaction::expense()
            ->whereMonth('due_date', $ref->month)
            ->whereYear('due_date', $ref->year)
            ->unpaid()
            ->sum('amount');

        $monthExpenseTotal = $monthExpensePaid + $monthExpensePending;

        $nextMonth = $ref->copy()->addMonth();
        $nextMonthExpensePending = (float) FinancialTransaction::expense()
            ->whereMonth('due_date', $nextMonth->month)
            ->whereYear('due_date', $nextMonth->year)
            ->unpaid()
            ->sum('amount');

        $nextMonthExpensePaid = (float) FinancialTransaction::expense()
            ->whereMonth('due_date', $nextMonth->month)
            ->whereYear('due_date', $nextMonth->year)
            ->paid()
            ->whereNotNull('account_id')
            ->sum('amount');

        $nextMonthExpenseTotal = $nextMonthExpensePaid + $nextMonthExpensePending;

        $salesData = $this->getSalesMonthData($referenceDate);

        $netProfitCurrent = $salesData['salesProfit'] - $monthExpensePaid;
        $netProfitProjected = $salesData['salesProfit'] - $monthExpenseTotal;

        $dueSoon = FinancialTransaction::unpaid()
            ->where('due_date', '<=', ($isCurrentMonth ? now() : $monthEnd)->copy()->addDays(7))
            ->where('due_date', '>=', $monthStart)
            ->with(['category', 'account'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $recentTransactions = FinancialTransaction::with(['category', 'account'])
            ->whereIn('status', ['paid', 'pending', 'overdue'])
            ->where(function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('due_date', [$monthStart, $monthEnd])
                  ->orWhereBetween('paid_at', [$monthStart, $monthEnd->copy()->endOfDay()]);
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $chartData = $this->getChartDataForMonth($ref);

        return compact(
            'accounts',
            'totalBalance',
            'monthIncome',
            'monthExpensePaid',
            'monthExpenseTotal',
            'monthExpensePending',
            'monthProfit',
            'netProfitCurrent',
            'netProfitProjected',
            'nextMonthExpenseTotal',
            'nextMonthExpensePending',
            'salesData',
            'dueSoon',
            'recentTransactions',
            'chartData',
        );
    }

    public function getSalesMonthData(?\Carbon\Carbon $referenceDate = null): array
    {
        $ref = $referenceDate ?? now();
        $monthStart = $ref->copy()->startOfMonth();
        $monthEnd = $ref->copy()->endOfMonth();

        $sales = Sale::with('items')
            ->paid()
            ->whereBetween('sold_at', [$monthStart, $monthEnd->copy()->endOfDay()])
            ->get();

        $salesCount = $sales->count();
        $salesRevenue = (float) $sales->sum('total');
        $salesCost = (float) $sales->sum(fn ($sale) => $sale->total_cost);
        $salesProfit = $salesRevenue - $salesCost;
        $salesMargin = $salesRevenue > 0 ? round(($salesProfit / $salesRevenue) * 100, 1) : 0;
        $tradeInTotal = (float) $sales->sum('trade_in_value');

        return compact('salesCount', 'salesRevenue', 'salesCost', 'salesProfit', 'salesMargin', 'tradeInTotal');
    }

    public function getChartData(int $days): array
    {
        $labels = [];
        $incomes = [];
        $expenses = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');

            $incomes[] = (float) FinancialTransaction::income()
                ->paid()
                ->whereNotNull('account_id')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');

            $expenses[] = (float) FinancialTransaction::expense()
                ->paid()
                ->whereNotNull('account_id')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');
        }

        return compact('labels', 'incomes', 'expenses');
    }

    public function getChartDataForMonth(\Carbon\Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth();
        $end = $ref->copy()->endOfMonth();
        $isCurrentMonth = $ref->isSameMonth(now());
        $lastDay = $isCurrentMonth ? min(now()->day, $end->day) : $end->day;

        $labels = [];
        $incomes = [];
        $expenses = [];

        for ($day = 1; $day <= $lastDay; $day++) {
            $date = $start->copy()->day($day);
            $labels[] = $date->format('d/m');

            $incomes[] = (float) FinancialTransaction::income()
                ->paid()
                ->whereNotNull('account_id')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');

            $expenses[] = (float) FinancialTransaction::expense()
                ->paid()
                ->whereNotNull('account_id')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');
        }

        return compact('labels', 'incomes', 'expenses');
    }

    // ─── Contas a Pagar / Receber ───

    public function getPayables(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->getTransactions('expense', $perPage, $filters);
    }

    public function getReceivables(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->getTransactions('income', $perPage, $filters);
    }

    private const SYSTEM_EXPENSE_CATEGORIES = ['Trade-in', 'Custo de Mercadoria', 'Compra Fornecedor'];

    private function getTransactions(string $type, int $perPage, array $filters): LengthAwarePaginator
    {
        $query = FinancialTransaction::with(['category', 'account', 'user'])
            ->where('type', $type);

        if ($type === 'expense') {
            $this->excludeSystemExpenseCategories($query);
        }

        $statusFilter = $filters['status'] ?? null;
        
        if ($statusFilter && trim($statusFilter) !== '') {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['pending', 'overdue', 'paid']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->where('due_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('due_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        $query->orderByRaw("FIELD(status, 'overdue', 'pending', 'paid', 'cancelled')");

        if ($type === 'expense') {
            $query->orderByRaw("CASE WHEN paid_at IS NOT NULL THEN paid_at END DESC");
        }

        return $query->orderBy('due_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function excludeSystemExpenseCategories($query): void
    {
        $categoryIds = FinancialCategory::whereIn('name', self::SYSTEM_EXPENSE_CATEGORIES)
            ->pluck('id');

        if ($categoryIds->isNotEmpty()) {
            $query->whereNotIn('category_id', $categoryIds);
        }
    }

    public function getPayablesSummary(array $filters = []): array
    {
        $query = FinancialTransaction::expense();
        $this->excludeSystemExpenseCategories($query);
        
        if (!empty($filters['start_date'])) {
            $query->where('due_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('due_date', '<=', $filters['end_date']);
        }
        
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }
        
        $statusFilter = $filters['status'] ?? null;
        if ($statusFilter && trim($statusFilter) !== '') {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['pending', 'overdue', 'paid']);
        }
        
        $pending = (float) (clone $query)->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $paidInPeriod = (float) (clone $query)->where('status', 'paid')->sum('amount');
        
        $overdueQuery = FinancialTransaction::expense()->overdue();
        $this->excludeSystemExpenseCategories($overdueQuery);
        $overdue = (float) $overdueQuery->sum('amount');

        return compact('pending', 'overdue', 'paidInPeriod');
    }

    public function getReceivablesSummary(array $filters = []): array
    {
        $query = FinancialTransaction::income();
        
        if (!empty($filters['start_date'])) {
            $query->where('due_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('due_date', '<=', $filters['end_date']);
        }
        
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }
        
        $statusFilter = $filters['status'] ?? null;
        if ($statusFilter && trim($statusFilter) !== '') {
            $query->where('status', $statusFilter);
        } else {
            $query->whereIn('status', ['pending', 'overdue', 'paid']);
        }
        
        $pending = (float) (clone $query)->whereIn('status', ['pending', 'overdue'])->sum('amount');
        $receivedInPeriod = (float) (clone $query)->where('status', 'paid')->sum('amount');
        $overdue = (float) FinancialTransaction::income()->overdue()->sum('amount');

        return compact('pending', 'overdue', 'receivedInPeriod');
    }

    // ─── Transactions CRUD ───

    public function createTransaction(array $data): FinancialTransaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = FinancialTransaction::create($data);

            // Se já pago, atualizar saldo da conta
            if ($transaction->status === TransactionStatus::Paid && $transaction->account_id) {
                $this->applyTransactionToAccount($transaction);
            }

            return $transaction;
        });
    }

    public function markAsPaid(FinancialTransaction $transaction, string $accountId, ?string $paymentMethod = null): void
    {
        // Guard: não permitir pagar transação já paga ou cancelada
        if (in_array($transaction->status, [TransactionStatus::Paid, TransactionStatus::Cancelled])) {
            return;
        }

        DB::transaction(function () use ($transaction, $accountId, $paymentMethod) {
            $transaction->update([
                'status' => TransactionStatus::Paid,
                'account_id' => $accountId,
                'paid_at' => now(),
                'payment_method' => $paymentMethod,
            ]);

            $this->applyTransactionToAccount($transaction);
        });
    }

    public function cancelTransaction(FinancialTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Se estava pago, reverter o saldo
            if ($transaction->status === TransactionStatus::Paid && $transaction->account_id) {
                $this->reverseTransactionFromAccount($transaction);
            }

            $transaction->update(['status' => TransactionStatus::Cancelled]);
        });
    }

    private function applyTransactionToAccount(FinancialTransaction $transaction): void
    {
        $account = FinancialAccount::find($transaction->account_id);
        if (!$account) {
            return;
        }

        if ($transaction->type === TransactionType::Income) {
            $account->addBalance((float) $transaction->amount);
        } else {
            $account->subtractBalance((float) $transaction->amount);
        }
    }

    private function reverseTransactionFromAccount(FinancialTransaction $transaction): void
    {
        $account = FinancialAccount::find($transaction->account_id);
        if (!$account) {
            return;
        }

        if ($transaction->type === TransactionType::Income) {
            $account->subtractBalance((float) $transaction->amount);
        } else {
            $account->addBalance((float) $transaction->amount);
        }
    }

    // ─── Transfers ───

    public function createTransfer(string $fromAccountId, string $toAccountId, float $amount, string $userId, ?string $description = null): AccountTransfer
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('O valor da transferência deve ser positivo.');
        }

        if ($fromAccountId === $toAccountId) {
            throw new \InvalidArgumentException('Não é possível transferir para a mesma conta.');
        }

        return DB::transaction(function () use ($fromAccountId, $toAccountId, $amount, $userId, $description) {
            $fromAccount = FinancialAccount::lockForUpdate()->findOrFail($fromAccountId);
            $toAccount = FinancialAccount::lockForUpdate()->findOrFail($toAccountId);

            if ((float) $fromAccount->current_balance < $amount) {
                throw new \InvalidArgumentException("Saldo insuficiente na conta {$fromAccount->name}.");
            }

            $fromAccount->subtractBalance($amount);
            $toAccount->addBalance($amount);

            return AccountTransfer::create([
                'from_account_id' => $fromAccountId,
                'to_account_id' => $toAccountId,
                'user_id' => $userId,
                'amount' => $amount,
                'description' => $description ?? "Transferência: {$fromAccount->name} → {$toAccount->name}",
                'transferred_at' => now(),
            ]);
        });
    }

    // ─── Accounts ───

    public function getAccounts(): Collection
    {
        return FinancialAccount::active()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();
    }

    public function getAccountStatement(string $accountId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = FinancialTransaction::where('account_id', $accountId)
            ->where('status', 'paid')
            ->with('category');

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('paid_at', '<=', $endDate . ' 23:59:59');
        }

        $transactions = $query->orderByDesc('paid_at')->get();

        // Include transfers
        $transfersOut = AccountTransfer::where('from_account_id', $accountId)
            ->when($startDate, fn ($q) => $q->where('transferred_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('transferred_at', '<=', $endDate . ' 23:59:59'))
            ->with('toAccount')
            ->get()
            ->map(fn ($t) => (object) [
                'date' => $t->transferred_at,
                'description' => 'Transferência → ' . ($t->toAccount?->name ?? 'Conta removida'),
                'amount' => -$t->amount,
                'type' => 'transfer_out',
                'category_name' => 'Transferência',
                'category_color' => '#6b7280',
            ]);

        $transfersIn = AccountTransfer::where('to_account_id', $accountId)
            ->when($startDate, fn ($q) => $q->where('transferred_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('transferred_at', '<=', $endDate . ' 23:59:59'))
            ->with('fromAccount')
            ->get()
            ->map(fn ($t) => (object) [
                'date' => $t->transferred_at,
                'description' => 'Transferência ← ' . ($t->fromAccount?->name ?? 'Conta removida'),
                'amount' => $t->amount,
                'type' => 'transfer_in',
                'category_name' => 'Transferência',
                'category_color' => '#6b7280',
            ]);

        $allEntries = $transactions->map(fn ($t) => (object) [
            'date' => $t->paid_at,
            'description' => $t->description,
            'amount' => $t->type === TransactionType::Income ? $t->amount : -$t->amount,
            'type' => $t->type->value,
            'category_name' => $t->category->name ?? '-',
            'category_color' => $t->category->color ?? '#6b7280',
        ]);

        return $allEntries->concat($transfersOut)->concat($transfersIn)
            ->sortByDesc('date')
            ->values();
    }

    public function createAccount(array $data): FinancialAccount
    {
        $account = FinancialAccount::create(array_merge($data, [
            'current_balance' => $data['initial_balance'] ?? 0,
        ]));

        // Se marcou como padrão, desmarca as outras
        if ($account->is_default) {
            FinancialAccount::where('id', '!=', $account->id)->update(['is_default' => false]);
        }

        return $account;
    }

    // ─── Categories ───

    public function getCategories(): Collection
    {
        return FinancialCategory::active()->orderBy('type')->orderBy('name')->get();
    }

    public function getCategoriesByType(string $type): Collection
    {
        return FinancialCategory::active()->where('type', $type)->orderBy('name')->get();
    }

    public function createCategory(array $data): FinancialCategory
    {
        return FinancialCategory::create($data);
    }

    public function updateCategory(FinancialCategory $category, array $data): void
    {
        if ($category->is_system) {
            // Categorias do sistema: só pode alterar cor e ícone
            $category->update(collect($data)->only(['color', 'icon'])->toArray());
        } else {
            $category->update($data);
        }
    }

    public function deleteCategory(FinancialCategory $category): bool
    {
        if ($category->is_system) {
            return false;
        }

        if ($category->transactions()->exists()) {
            $category->update(['is_active' => false]);
            return true;
        }

        $category->delete();
        return true;
    }

    // ─── Inventário Seminovos ───

    public function getInventoryData(): array
    {
        $ownProducts = Product::active()->inStock()
            ->whereIn('condition', ['used', 'refurbished'])
            ->get();

        $consignmentItems = ConsignmentStockItem::available()->used()->get();

        $ownData = $this->calculateInventoryGroup($ownProducts, 'own');
        $consignmentData = $this->calculateInventoryGroup($consignmentItems, 'consignment');

        $totalItems = $ownData['itemCount'] + $consignmentData['itemCount'];
        $totalCost = $ownData['totalCost'] + $consignmentData['totalCost'];
        $totalSaleValue = $ownData['totalSaleValue'] + $consignmentData['totalSaleValue'];
        $potentialProfit = $totalSaleValue - $totalCost;
        $potentialMargin = $totalSaleValue > 0
            ? round(($potentialProfit / $totalSaleValue) * 100, 1)
            : 0;

        $allDays = array_merge($ownData['daysInStock'], $consignmentData['daysInStock']);
        $avgDaysInStock = count($allDays) > 0 ? (int) round(array_sum($allDays) / count($allDays)) : 0;

        $withoutPrice = $ownData['withoutPrice'] + $consignmentData['withoutPrice'];
        $itemsWithoutPrice = array_merge($ownData['itemsWithoutPrice'], $consignmentData['itemsWithoutPrice']);

        return compact(
            'totalItems', 'totalCost', 'totalSaleValue',
            'potentialProfit', 'potentialMargin', 'avgDaysInStock',
            'withoutPrice', 'itemsWithoutPrice', 'ownData', 'consignmentData',
        );
    }

    private function calculateInventoryGroup(Collection $items, string $type): array
    {
        $isOwn = $type === 'own';
        $itemCount = 0;
        $totalCost = 0.0;
        $totalSaleValue = 0.0;
        $daysInStock = [];
        $withoutPrice = 0;
        $itemsWithoutPrice = [];

        foreach ($items as $item) {
            $qty = $isOwn ? (int) $item->stock_quantity : (int) $item->available_quantity;
            $cost = (float) ($isOwn ? $item->cost_price : $item->supplier_cost);
            $sale = (float) ($isOwn ? $item->sale_price : $item->suggested_price);

            $itemCount += $qty;
            $totalCost += $cost * $qty;
            $totalSaleValue += $sale * $qty;

            if ($sale <= 0) {
                $withoutPrice += $qty;
                $itemsWithoutPrice[] = [
                    'id' => $item->id,
                    'name' => $isOwn ? $item->full_name : $item->full_name,
                    'cost' => $cost,
                    'type' => $isOwn ? 'own' : 'consignment',
                ];
            }

            $dateRef = $isOwn ? $item->created_at : $item->received_at;
            if ($dateRef) {
                $daysInStock[] = (int) $dateRef->diffInDays(now());
            }
        }

        $profit = $totalSaleValue - $totalCost;
        $margin = $totalSaleValue > 0 ? round(($profit / $totalSaleValue) * 100, 1) : 0;

        return compact('itemCount', 'totalCost', 'totalSaleValue', 'profit', 'margin', 'daysInStock', 'withoutPrice', 'itemsWithoutPrice');
    }

    // ─── Overdue check ───

    public function markOverdueTransactions(): int
    {
        return FinancialTransaction::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);
    }

    // ─── Integration helpers ───

    public function getDefaultAccount(): ?FinancialAccount
    {
        return FinancialAccount::default()->first()
            ?? FinancialAccount::active()->first();
    }

    public function getCategoryByName(string $name, string $type): ?FinancialCategory
    {
        return FinancialCategory::where('name', $name)
            ->where('type', $type)
            ->first();
    }

    public function registerSaleIncome(
        string $userId,
        float $amount,
        string $description,
        ?string $referenceId = null,
        ?string $paymentMethod = null,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        $category = $this->getCategoryByName('Venda', 'income');
        $account = $this->getDefaultAccount();
        $date = $date ?? now();

        if (!$category) {
            return null;
        }

        return $this->createTransaction([
            'account_id' => $account?->id,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'income',
            'status' => $account ? 'paid' : 'pending',
            'amount' => $amount,
            'description' => $description,
            'due_date' => $date->format('Y-m-d'),
            'paid_at' => $account ? $date : null,
            'payment_method' => $paymentMethod,
            'reference_type' => 'Sale',
            'reference_id' => $referenceId,
        ]);
    }

    public function registerSaleCost(
        string $userId,
        float $amount,
        string $description,
        ?string $referenceId = null,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        if ($amount <= 0) {
            return null;
        }

        $category = $this->getCategoryByName('Custo de Mercadoria', 'expense');
        $date = $date ?? now();

        if (!$category) {
            return null;
        }

        // CMV é registro contábil para P&L, não movimenta caixa
        return $this->createTransaction([
            'account_id' => null,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'expense',
            'status' => 'paid',
            'amount' => $amount,
            'description' => $description,
            'due_date' => $date->format('Y-m-d'),
            'paid_at' => $date,
            'reference_type' => 'Sale',
            'reference_id' => $referenceId,
        ]);
    }

    public function registerTradeInExpense(
        string $userId,
        float $amount,
        string $description,
        ?string $referenceId = null,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        $category = $this->getCategoryByName('Trade-in', 'expense');
        $date = $date ?? now();

        if (!$category) {
            return null;
        }

        // Trade-in é troca de aparelho, não saída de caixa
        return $this->createTransaction([
            'account_id' => null,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'expense',
            'status' => 'paid',
            'amount' => $amount,
            'description' => $description,
            'due_date' => $date->format('Y-m-d'),
            'paid_at' => $date,
            'reference_type' => 'Sale',
            'reference_id' => $referenceId,
        ]);
    }

    public function registerReservationPayment(
        string $userId,
        float $amount,
        string $description,
        ?string $referenceId = null,
        ?string $paymentMethod = null,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        $category = $this->getCategoryByName('Sinal de Reserva', 'income');
        $account = $this->getDefaultAccount();
        $date = $date ?? now();

        if (!$category) {
            return null;
        }

        return $this->createTransaction([
            'account_id' => $account?->id,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'income',
            'status' => $account ? 'paid' : 'pending',
            'amount' => $amount,
            'description' => $description,
            'due_date' => $date->format('Y-m-d'),
            'paid_at' => $account ? $date : null,
            'payment_method' => $paymentMethod,
            'reference_type' => 'Reservation',
            'reference_id' => $referenceId,
        ]);
    }

    public function cancelSaleTransactions(string $saleId): void
    {
        $transactions = FinancialTransaction::where('reference_type', 'Sale')
            ->where('reference_id', $saleId)
            ->whereIn('status', ['paid', 'pending'])
            ->get();

        foreach ($transactions as $transaction) {
            $this->cancelTransaction($transaction);
        }
    }

    public function cancelReservationTransactions(string $reservationId): void
    {
        $transactions = FinancialTransaction::where('reference_type', 'Reservation')
            ->where('reference_id', $reservationId)
            ->whereIn('status', ['paid', 'pending'])
            ->get();

        foreach ($transactions as $transaction) {
            $this->cancelTransaction($transaction);
        }
    }

    // ─── Import integration ───

    public function registerImportExpense(
        string $userId,
        float $amount,
        string $description,
        ?string $referenceId = null,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        $category = $this->getCategoryByName('Compra Fornecedor', 'expense');
        $account = $this->getDefaultAccount();
        $date = $date ?? now();

        if (! $category) {
            return null;
        }

        return $this->createTransaction([
            'account_id' => $account?->id,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'expense',
            'status' => $account ? 'paid' : 'pending',
            'amount' => $amount,
            'description' => $description,
            'due_date' => $date->format('Y-m-d'),
            'paid_at' => $account ? $date : null,
            'reference_type' => 'ImportOrder',
            'reference_id' => $referenceId,
        ]);
    }

    public function registerCommissionPayment(
        string $userId,
        float $amount,
        string $internName,
        string $withdrawalId,
        ?\DateTimeInterface $date = null,
    ): ?FinancialTransaction {
        $category = $this->getCategoryByName('Comissão', 'expense');

        if (!$category) {
            $category = FinancialCategory::create([
                'name' => 'Comissão',
                'type' => 'expense',
                'color' => '#7c3aed',
                'is_active' => true,
                'is_system' => false,
            ]);
        }

        $account = $this->getDefaultAccount();
        $date = $date ?? now();

        return $this->createTransaction([
            'account_id' => $account?->id,
            'category_id' => $category->id,
            'user_id' => $userId,
            'type' => 'expense',
            'status' => 'paid',
            'amount' => $amount,
            'description' => "Pagamento de comissão - {$internName}",
            'due_date' => $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : now()->format('Y-m-d'),
            'paid_at' => $date,
            'reference_type' => 'CommissionWithdrawal',
            'reference_id' => $withdrawalId,
        ]);
    }

    public function cancelImportTransactions(string $importOrderId): void
    {
        $transactions = FinancialTransaction::where('reference_type', 'ImportOrder')
            ->where('reference_id', $importOrderId)
            ->whereIn('status', ['paid', 'pending'])
            ->get();

        foreach ($transactions as $transaction) {
            $this->cancelTransaction($transaction);
        }
    }
}
