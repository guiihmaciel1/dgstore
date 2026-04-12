<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\Services\CustomerService;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Domain\Reservation\Enums\ReservationStatus;
use App\Domain\Reservation\Models\Reservation;
use App\Domain\Reservation\Models\ReservationPayment;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Supplier\Services\QuotationService;
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
        private readonly CustomerService $customerService,
        private readonly QuotationService $quotationService,
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'source' => $request->get('source'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
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

        // Busca a melhor cotação para cada reserva ativa
        $productNames = $reservations->getCollection()
            ->filter(fn ($r) => $r->status === ReservationStatus::Active)
            ->map(fn ($r) => $r->product_description ?? $r->product?->full_name ?? $r->product?->name)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $bestQuotationsMap = $this->quotationService->getBestQuotationsForProducts($productNames);

        return view('reservations.index', [
            'reservations' => $reservations,
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => ReservationStatus::cases(),
            'bestQuotationsMap' => $bestQuotationsMap,
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
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            $reservation = $this->reservationService->create($validated);

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
        $reservation->load(['customer', 'product', 'user', 'payments.user', 'convertedSale', 'items.product']);

        $productName = $reservation->product_description
            ?? $reservation->product?->full_name
            ?? $reservation->product?->name;

        $bestQuotations = $productName
            ? $this->quotationService->getBestQuotationsForProduct($productName)
            : collect();

        return view('reservations.show', [
            'reservation' => $reservation,
            'paymentMethods' => PaymentMethod::cases(),
            'bestQuotations' => $bestQuotations,
        ]);
    }

    public function edit(Reservation $reservation): View|RedirectResponse
    {
        if ($reservation->status === ReservationStatus::Converted) {
            return redirect()
                ->route('reservations.show', $reservation)
                ->with('error', 'Reservas já convertidas em venda não podem ser editadas.');
        }

        if ($reservation->status === ReservationStatus::Cancelled) {
            return redirect()
                ->route('reservations.show', $reservation)
                ->with('error', 'Reservas canceladas não podem ser editadas.');
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
            ->route('sales.create', array_filter([
                'from_reservation' => $reservation->id,
            ]));
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
        $excludeIds = $request->get('exclude', []);

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $dbQuery = Product::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('imei', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            })
            ->active()
            ->where('reserved', false);

        if (!empty($excludeIds)) {
            $dbQuery->whereNotIn('id', $excludeIds);
        }

        $results = $dbQuery->orderByDesc('stock_quantity')
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->full_name,
                'sku' => $p->sku,
                'stock' => $p->stock_quantity,
                'cost_price' => (float) $p->cost_price,
                'sale_price' => (float) $p->sale_price,
                'condition' => $p->condition instanceof \BackedEnum ? $p->condition->value : $p->condition,
                'formatted_price' => $p->sale_price > 0 ? 'R$ ' . number_format((float) $p->sale_price, 2, ',', '.') : null,
                'formatted_cost' => $p->cost_price > 0 ? 'R$ ' . number_format((float) $p->cost_price, 2, ',', '.') : null,
            ]);

        return response()->json($results);
    }
}
