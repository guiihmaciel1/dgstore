<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Services\CustomerService;
use App\Domain\Product\Services\ProductService;
use App\Domain\Reservation\Enums\ReservationStatus;
use App\Domain\Reservation\Models\Reservation;
use App\Domain\Reservation\Models\ReservationPayment;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Supplier\Models\Quotation;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreReservationRequest;
use App\Presentation\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService,
        private readonly ProductService $productService,
        private readonly CustomerService $customerService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'active_only' => $request->boolean('active_only'),
            'sort' => $request->get('sort', 'created_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        try {
            $reservations = $this->reservationService->list(15, $filters);
        } catch (\Throwable $e) {
            // Fallback sem busca em caso de colunas faltando
            $reservations = $this->reservationService->list(15, array_merge($filters, ['search' => null]));
        }

        $stats = [
            'active' => $this->reservationService->countActive(),
            'expiring_soon' => $this->reservationService->countExpiringSoon(3),
            'overdue' => $this->reservationService->countOverdue(),
        ];

        return view('reservations.index', [
            'reservations' => $reservations,
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => ReservationStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedProduct = $request->get('product_id')
            ? $this->productService->find($request->get('product_id'))
            : null;

        return view('reservations.create', [
            'selectedProduct' => $selectedProduct,
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        try {
            $data = $request->only([
                'customer_id', 'product_description',
                'source', 'product_price', 'cost_price', 'deposit_amount',
                'expires_at', 'notes'
            ]);

            $data['user_id'] = auth()->id();

            // product_id só se for válido
            $productId = $request->input('product_id');
            if ($productId) {
                $data['product_id'] = $productId;
            }

            // Pagamento inicial só se tiver valor > 0
            $initialPayment = (float) $request->input('initial_payment', 0);
            if ($initialPayment > 0) {
                $data['initial_payment'] = $initialPayment;
                $data['payment_method'] = $request->input('payment_method', 'pix');
            }

            $reservation = $this->reservationService->create($data);

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', "Reserva #{$reservation->reservation_number} criada com sucesso!");
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao criar reserva: ' . $e->getMessage());
        }
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['customer', 'product', 'user', 'payments.user', 'convertedSale']);

        return view('reservations.show', [
            'reservation' => $reservation,
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function edit(Reservation $reservation): View|RedirectResponse
    {
        if (!$reservation->isActive()) {
            return redirect()
                ->route('reservations.show', $reservation)
                ->with('error', 'Apenas reservas ativas podem ser editadas.');
        }

        $reservation->load(['customer', 'product', 'user', 'payments']);

        return view('reservations.edit', [
            'reservation' => $reservation,
        ]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        try {
            $this->reservationService->update($reservation, $request->validated());

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', 'Reserva atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroyPayment(Reservation $reservation, ReservationPayment $payment): RedirectResponse
    {
        try {
            $this->reservationService->reversePayment($reservation, $payment);

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', 'Pagamento estornado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function storePayment(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,credit_card,debit_card,pix,bank_transfer'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->reservationService->addPayment(
                $reservation,
                auth()->id(),
                $request->amount,
                PaymentMethod::from($request->payment_method),
                $request->notes
            );

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', 'Pagamento registrado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function convert(Reservation $reservation): RedirectResponse
    {
        return redirect()
            ->route('sales.create', [
                'from_reservation' => $reservation->id,
                'customer_id' => $reservation->customer_id,
                'product_id' => $reservation->product_id,
                'discount' => $reservation->deposit_paid,
            ]);
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        try {
            $this->reservationService->cancel($reservation);

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', 'Reserva cancelada com sucesso. O produto foi liberado.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = $this->customerService->search($query)->take(10);

        return response()->json($customers->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'phone' => $c->formatted_phone,
        ]));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Busca produtos do estoque (incluindo sem estoque)
        $stockProducts = $this->productService->search($query)
            ->filter(fn($p) => !$p->reserved)
            ->take(10)
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->full_name,
                'sku' => $p->sku,
                'stock' => $p->stock_quantity,
                'source' => 'stock',
                'source_label' => $p->stock_quantity > 0 ? 'Estoque' : 'Sem estoque',
            ])->values();

        // Busca cotações de fornecedores (últimos preços únicos)
        $quotations = Quotation::with('supplier')
            ->where('product_name', 'like', "%{$query}%")
            ->orderByDesc('quoted_at')
            ->limit(20)
            ->get()
            ->unique('product_name')
            ->take(10)
            ->map(function ($q) {
                $basePrice = (float) $q->unit_price;
                $finalPrice = round($basePrice * 1.04, 2);

                return [
                    'id' => null,
                    'name' => $q->product_name,
                    'sku' => $q->supplier->name ?? 'Fornecedor',
                    'price' => $basePrice,
                    'final_price' => $finalPrice,
                    'formatted_price' => 'R$ ' . number_format($finalPrice, 2, ',', '.'),
                    'formatted_base_price' => $q->formatted_unit_price,
                    'stock' => null,
                    'source' => 'quotation',
                    'source_label' => 'Cotação - ' . ($q->supplier->name ?? ''),
                ];
            })->values();

        // Mescla resultados: estoque primeiro, depois cotações
        $results = $stockProducts->concat($quotations)->take(15);

        return response()->json($results);
    }
}
