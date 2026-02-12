<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Services\CustomerService;
use App\Domain\Product\Services\ProductService;
use App\Domain\Reservation\Enums\ReservationStatus;
use App\Domain\Reservation\Models\Reservation;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
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

        $reservations = $this->reservationService->list(15, $filters);

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
        $products = $this->productService->getActiveProducts()
            ->filter(fn($p) => !$p->reserved && $p->stock_quantity > 0);

        // Pre-seleciona produto se vier da URL
        $selectedProduct = $request->get('product_id') 
            ? $this->productService->find($request->get('product_id'))
            : null;

        return view('reservations.create', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'product_id' => ['required', 'exists:products,id'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'expires_at' => ['required', 'date', 'after:today'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required_with:initial_payment', 'in:cash,credit_card,debit_card,pix,bank_transfer'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $reservation = $this->reservationService->create(
                array_merge(
                    $request->only([
                        'customer_id', 'product_id', 'product_price',
                        'deposit_amount', 'expires_at', 'initial_payment',
                        'payment_method', 'notes'
                    ]),
                    ['user_id' => auth()->id()]
                )
            );

            return redirect()
                ->route('reservations.show', $reservation)
                ->with('success', "Reserva #{$reservation->reservation_number} criada com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
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
        // Redireciona para tela de venda com dados pré-preenchidos
        return redirect()
            ->route('sales.create', [
                'from_reservation' => $reservation->id,
                'customer_id' => $reservation->customer_id,
                'product_id' => $reservation->product_id,
                'discount' => $reservation->deposit_paid, // Sinal como desconto
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

        $products = $this->productService->search($query)
            ->filter(fn($p) => !$p->reserved && $p->stock_quantity > 0)
            ->take(10);

        return response()->json($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->full_name,
            'sku' => $p->sku,
            'price' => $p->sale_price,
            'formatted_price' => $p->formatted_sale_price,
            'stock' => $p->stock_quantity,
        ])->values());
    }
}
