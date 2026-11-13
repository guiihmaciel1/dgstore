<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Finance\Models\FinancialAccount;
use App\Domain\Finance\Services\FinanceService;
use App\Domain\Finance\Services\StoreCapitalService;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Purchase\Models\Purchase;
use App\Domain\Purchase\Models\PurchaseItem;
use App\Domain\Purchase\Services\PurchaseService;
use App\Domain\Supplier\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases) {}

    public function index(Request $request): View
    {
        $status = $request->input('status', 'open');

        $query = Purchase::with('supplier')
            ->withCount([
                'items as units_total' => fn ($q) => $q->where('status', '!=', PurchaseItem::STATUS_CANCELLED),
                'items as units_received' => fn ($q) => $q->where('status', PurchaseItem::STATUS_RECEIVED),
            ])
            ->orderByDesc('purchased_at')
            ->orderByDesc('number');

        match ($status) {
            'open' => $query->open(),
            'all' => null,
            default => $query->where('status', $status),
        };

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhereHas('items', fn ($i) => $i->where('model', 'like', "%{$search}%"));
            });
        }

        return view('purchases.index', [
            'purchases' => $query->paginate(20)->withQueryString(),
            'summary' => $this->purchases->pendingSummary(),
            'status' => $status,
            'search' => $search ?? '',
        ]);
    }

    public function create(FinanceService $finance): View
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'accounts' => FinancialAccount::active()->orderBy('name')->get(['id', 'name', 'is_default']),
            'defaultAccountId' => $finance->getDefaultAccount()?->id,
            'categories' => collect(ProductCategory::cases())->reject(fn ($c) => $c === ProductCategory::Service)->values(),
            'conditions' => ProductCondition::cases(),
            'unitCategories' => PurchaseItem::UNIT_CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'items' => collect($request->input('items', []))->map(function ($item) {
                $item['unit_cost'] = StoreCapitalService::parseMoney($item['unit_cost'] ?? null);
                return $item;
            })->all(),
        ]);

        $validated = $request->validate([
            'supplier_id' => ['nullable', 'string', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'purchased_at' => ['required', 'date', 'before_or_equal:today'],
            'expected_at' => ['nullable', 'date', 'after_or_equal:purchased_at'],
            'account_id' => ['nullable', 'string', 'exists:financial_accounts,id'],
            'register_expense' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.category' => ['required', Rule::enum(ProductCategory::class)],
            'items.*.condition' => ['required', Rule::enum(ProductCondition::class)],
            'items.*.model' => ['required', 'string', 'max:100'],
            'items.*.storage' => ['nullable', 'string', 'max:50'],
            'items.*.color' => ['nullable', 'string', 'max:50'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:200'],
        ], [
            'items.required' => 'Adicione ao menos um item.',
            'items.*.model.required' => 'Informe o modelo.',
            'items.*.unit_cost.required' => 'Informe o custo.',
            'items.*.unit_cost.numeric' => 'Custo inválido (ex.: 6.500,00).',
            'purchased_at.before_or_equal' => 'A data da compra não pode ser no futuro.',
        ]);

        if (empty($validated['supplier_id']) && empty(trim((string) ($validated['supplier_name'] ?? '')))) {
            return back()->withInput()->withErrors(['supplier_name' => 'Escolha um fornecedor ou digite o nome.']);
        }

        $registerExpense = $request->boolean('register_expense');
        if ($registerExpense && empty($validated['account_id'])) {
            return back()->withInput()->withErrors(['account_id' => 'Escolha de qual conta saiu o dinheiro.']);
        }

        $purchase = $this->purchases->create(
            $validated + ['register_expense' => $registerExpense],
            $validated['items'],
            $request->user(),
        );

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', "Compra {$purchase->number} registrada"
                . ($purchase->financial_transaction_id ? ' e lançada no financeiro.' : '.'));
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['items.product', 'supplier', 'account', 'user']);

        return view('purchases.show', ['purchase' => $purchase]);
    }

    public function cancelItem(Request $request, PurchaseItem $item): RedirectResponse
    {
        try {
            $this->purchases->cancelItem($item, $request->boolean('refund'), $request->user());
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $request->boolean('refund')
            ? 'Item cancelado e valor estornado no financeiro.'
            : 'Item cancelado (sem estorno: fica como crédito com o fornecedor).');
    }
}
