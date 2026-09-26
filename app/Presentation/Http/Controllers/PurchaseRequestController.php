<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Services\CustomerService;
use App\Domain\PurchaseRequest\Enums\PurchaseRequestStatus;
use App\Domain\PurchaseRequest\Models\PurchaseRequest;
use App\Domain\PurchaseRequest\Services\PurchaseRequestService;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StorePurchaseRequestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseRequestController extends Controller
{
    public function __construct(
        private readonly PurchaseRequestService $service,
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->get('status'),
            'type' => $request->get('type'),
            'seller_id' => $request->get('seller_id'),
            'search' => $request->get('search'),
        ];

        $purchaseRequests = $this->service->list(20, $filters);

        $sellers = User::whereIn('role', [
            UserRole::AdminGeral->value,
            UserRole::Seller->value,
            UserRole::Intern->value,
        ])->orderBy('name')->get(['id', 'name']);

        $pendingCount = $this->service->pendingCount();

        return view('purchase-requests.index', [
            'purchaseRequests' => $purchaseRequests,
            'sellers' => $sellers,
            'filters' => $filters,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function create(): View
    {
        return view('purchase-requests.create');
    }

    public function store(StorePurchaseRequestRequest $request): RedirectResponse
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

            // Calcular diferença de upgrade
            $upgradeDifference = null;
            if ($validated['type'] === 'upgrade' && !empty($validated['trade_in_value'])) {
                $upgradeDifference = (float) $validated['sale_price'] - (float) $validated['trade_in_value'];
                if ($upgradeDifference < 0) {
                    $upgradeDifference = 0;
                }
            }

            $purchaseRequest = $this->service->create([
                'customer_id' => $customerId,
                'seller_id' => auth()->id(),
                'seller_name' => auth()->user()->name,
                'type' => $validated['type'],
                'desired_product' => $validated['desired_product'],
                'desired_model' => $validated['desired_model'] ?? null,
                'desired_storage' => $validated['desired_storage'] ?? null,
                'desired_color' => $validated['desired_color'] ?? null,
                'desired_condition' => $validated['desired_condition'],
                'estimated_cost' => $validated['estimated_cost'] ?? null,
                'sale_price' => $validated['sale_price'],
                'down_payment' => $validated['down_payment'],
                'down_payment_method' => $validated['down_payment_method'],
                'payment_method' => $validated['payment_method'],
                'installments' => $validated['installments'] ?? null,
                'trade_in_device' => $validated['trade_in_device'] ?? null,
                'trade_in_value' => $validated['trade_in_value'] ?? null,
                'upgrade_difference' => $upgradeDifference,
                'delivery_type' => $validated['delivery_type'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'delivery_time' => $validated['delivery_time'] ?? null,
                'delivery_notes' => $validated['delivery_notes'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => PurchaseRequestStatus::Pending,
            ]);

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', "Solicitação #{$purchaseRequest->request_number} registrada! Aguardando aprovação.");
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao registrar solicitação: ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest): View
    {
        $purchaseRequest->load(['customer', 'seller', 'approver', 'convertedSale']);

        return view('purchase-requests.show', [
            'purchaseRequest' => $purchaseRequest,
        ]);
    }

    public function approve(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Apenas administradores podem aprovar solicitações.');
        }

        try {
            $this->service->approve(
                $purchaseRequest,
                auth()->id(),
                $request->get('admin_notes')
            );

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Solicitação aprovada! A compra no fornecedor está autorizada.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Apenas administradores podem rejeitar solicitações.');
        }

        try {
            $this->service->reject(
                $purchaseRequest,
                auth()->id(),
                $request->get('reason')
            );

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Solicitação rejeitada.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markPurchased(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        try {
            $this->service->markPurchased($purchaseRequest);

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Marcada como comprada! Agora é só entregar ao cliente.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function markDelivered(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        try {
            $this->service->markDelivered($purchaseRequest);

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Marcada como entregue! Agora efetive a venda.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function convert(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        if (!$purchaseRequest->canBeConverted()) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Apenas solicitações entregues podem ser efetivadas.');
        }

        return redirect()->route('sales.create', [
            'from_purchase_request' => $purchaseRequest->id,
        ]);
    }

    public function cancel(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        $isOwnerOrAdmin = auth()->user()->isAdmin() || auth()->id() === $purchaseRequest->seller_id;

        if (!$isOwnerOrAdmin) {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Apenas admins ou a vendedora responsável podem cancelar.');
        }

        try {
            $this->service->cancel($purchaseRequest, $request->get('reason'));

            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Solicitação cancelada.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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
            'address' => $c->address,
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
