<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Services\CustomerService;
use App\Domain\Payment\Services\CardFeeCalculatorService;
use App\Domain\PreSale\Enums\PreSaleStatus;
use App\Domain\PreSale\Models\PreSale;
use App\Domain\PreSale\Services\PreSaleService;
use App\Domain\User\Models\User;
use App\Domain\User\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StorePreSaleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreSaleController extends Controller
{
    public function __construct(
        private readonly PreSaleService $preSaleService,
        private readonly CustomerService $customerService,
        private readonly CardFeeCalculatorService $cardFeeCalculator,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->get('status'),
            'seller_id' => $request->get('seller_id'),
            'search' => $request->get('search'),
        ];

        $preSales = $this->preSaleService->list(20, $filters);

        $sellers = User::whereIn('role', [
            UserRole::AdminGeral->value,
            UserRole::Seller->value,
            UserRole::Intern->value,
        ])->orderBy('name')->get(['id', 'name']);

        return view('pre-sales.index', [
            'preSales' => $preSales,
            'sellers' => $sellers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('pre-sales.create');
    }

    public function store(StorePreSaleRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Se não tem customer_id, cadastra cliente inline
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId && !empty($validated['customer_name'])) {
                $customer = $this->createInlineCustomer($validated);
                $customerId = $customer->id;
            }

            if (!$customerId) {
                return redirect()->back()->withInput()->with('error', 'Cliente é obrigatório.');
            }

            // Buscar produto para montar snapshot
            $productData = $this->preSaleService->searchByImei($validated['product_imei']);
            if (!$productData) {
                return redirect()->back()->withInput()->with('error', 'Produto não encontrado com o IMEI informado.');
            }

            if ($productData['reserved']) {
                return redirect()->back()->withInput()->with('error', 'Este produto já está reservado.');
            }

            // Montar trade-in (se houver)
            $tradeInDevice = null;
            if (!empty($validated['trade_in_model'])) {
                $tradeInDevice = [
                    'model' => $validated['trade_in_model'],
                    'value' => (float) ($validated['trade_in_value'] ?? 0),
                    'condition' => $validated['trade_in_condition'] ?? null,
                ];
            }

            $preSale = $this->preSaleService->create([
                'customer_id' => $customerId,
                'seller_id' => auth()->id(),
                'seller_name' => auth()->user()->name,
                'product_id' => $productData['product_id'],
                'consignment_item_id' => $productData['consignment_item_id'],
                'product_snapshot' => $productData,
                'product_imei' => $validated['product_imei'],
                'unit_price' => $validated['unit_price'],
                'cost_price' => $validated['cost_price'],
                'condition' => $validated['condition'],
                'down_payment' => $validated['down_payment'],
                'down_payment_method' => $validated['down_payment_method'],
                'payment_method' => $validated['payment_method'],
                'installments' => $validated['installments'] ?? null,
                'card_gross_amount' => $validated['card_gross_amount'] ?? null,
                'card_net_amount' => $validated['card_net_amount'] ?? null,
                'card_fee_rate' => $validated['card_fee_rate'] ?? null,
                'trade_in_device' => $tradeInDevice,
                'trade_in_value' => !empty($validated['trade_in_value']) ? $validated['trade_in_value'] : null,
                'final_balance' => $validated['final_balance'],
                'notes' => $validated['notes'] ?? null,
                'status' => PreSaleStatus::Pending,
            ]);

            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('success', "Pré-venda #{$preSale->pre_sale_number} registrada com sucesso!");
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao registrar pré-venda: ' . $e->getMessage());
        }
    }

    public function show(PreSale $preSale): View
    {
        $preSale->load(['customer', 'seller', 'convertedSale']);

        // Se custo salvo é zero, buscar da tabela de marketing
        if ((float) $preSale->cost_price <= 0) {
            $snapshot = $preSale->product_snapshot ?? [];
            $marketingCost = $this->preSaleService->getMarketingCost(
                $snapshot['name'] ?? $preSale->product_name,
                $snapshot['storage'] ?? null
            );
            if ($marketingCost) {
                $preSale->cost_price = $marketingCost;
            }
        }

        // Simular parcelamento sobre o saldo restante
        $installmentOptions = [];
        $balanceForCard = (float) $preSale->final_balance;
        if ($balanceForCard > 0) {
            try {
                $installmentOptions = $this->cardFeeCalculator->calculateAllOptions($balanceForCard);
            } catch (\Throwable $e) {
                // Ignora erros
            }
        }

        return view('pre-sales.show', [
            'preSale' => $preSale,
            'installmentOptions' => $installmentOptions,
        ]);
    }

    public function markReady(PreSale $preSale): RedirectResponse
    {
        if (!$preSale->isPending()) {
            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('error', 'Apenas pré-vendas pendentes podem ser marcadas como prontas.');
        }

        $this->preSaleService->markReady($preSale);

        return redirect()
            ->route('pre-sales.show', $preSale)
            ->with('success', 'Pré-venda marcada como pronta para lançamento!');
    }

    public function convert(PreSale $preSale): RedirectResponse
    {
        $isOwnerOrAdmin = auth()->user()->isAdmin() || auth()->id() === $preSale->seller_id;

        if (!$isOwnerOrAdmin) {
            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('error', 'Apenas admins ou a vendedora responsável podem efetivar.');
        }

        if (!$preSale->isActionable()) {
            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('error', 'Apenas pré-vendas pendentes ou prontas podem ser efetivadas.');
        }

        return redirect()->route('sales.create', [
            'from_presale' => $preSale->id,
        ]);
    }

    public function cancel(Request $request, PreSale $preSale): RedirectResponse
    {
        $isOwnerOrAdmin = auth()->user()->isAdmin() || auth()->id() === $preSale->seller_id;

        if (!$isOwnerOrAdmin) {
            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('error', 'Apenas admins ou a vendedora responsável podem cancelar.');
        }

        if (!$preSale->isActionable()) {
            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('error', 'Apenas pré-vendas pendentes ou prontas podem ser canceladas.');
        }

        try {
            $this->preSaleService->cancel($preSale, $request->get('reason'));

            return redirect()
                ->route('pre-sales.show', $preSale)
                ->with('success', 'Pré-venda cancelada. O produto foi liberado.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao cancelar: ' . $e->getMessage());
        }
    }

    public function searchByImei(Request $request): JsonResponse
    {
        $imei = trim($request->get('imei', ''));

        if (strlen($imei) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Informe pelo menos 8 dígitos do IMEI.',
            ]);
        }

        $result = $this->preSaleService->searchByImei($imei);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto encontrado com esse IMEI.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = $this->customerService->search($query)->take(10);

        return response()->json($customers->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'phone' => $c->formatted_phone,
            'cpf' => $c->cpf,
            'instagram' => $c->instagram,
        ]));
    }

    private function createInlineCustomer(array $data): Customer
    {
        return Customer::create([
            'name' => $data['customer_name'],
            'phone' => $data['customer_phone'],
            'cpf' => $data['customer_cpf'] ?? null,
            'instagram' => $data['customer_instagram'] ?? null,
        ]);
    }
}
