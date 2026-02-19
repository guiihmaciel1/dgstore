<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeCustomer;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeReservation;
use App\Domain\Perfumes\Models\PerfumeReservationPayment;
use App\Domain\Perfumes\Services\PerfumeReservationService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Perfumes\StorePerfumeReservationRequest;
use App\Presentation\Http\Requests\Perfumes\StorePerfumeReservationPaymentRequest;
use Illuminate\Http\Request;

class AdminPerfumeReservationController extends Controller
{
    public function __construct(
        private PerfumeReservationService $reservationService
    ) {}

    public function index(Request $request)
    {
        $query = PerfumeReservation::with(['customer', 'product', 'user']);

        if ($search = $request->get('search')) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $reservations = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'active'    => PerfumeReservation::where('status', 'active')->count(),
            'completed' => PerfumeReservation::where('status', 'completed')->count(),
            'cancelled' => PerfumeReservation::where('status', 'cancelled')->count(),
            'expired'   => PerfumeReservation::where('status', 'expired')->count(),
        ];

        return view('admin.perfumes.reservations.index', compact('reservations', 'stats'));
    }

    public function create()
    {
        $customers = PerfumeCustomer::orderBy('name')->get();
        $products = PerfumeProduct::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('admin.perfumes.reservations.create', compact('customers', 'products'));
    }

    public function store(StorePerfumeReservationRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $reservation = $this->reservationService->create($data);

        return redirect()->route('admin.perfumes.reservations.show', $reservation)
            ->with('success', 'Encomenda registrada com sucesso.');
    }

    public function show(PerfumeReservation $reservation)
    {
        $reservation->load(['customer', 'product', 'user', 'payments.user', 'convertedSale']);

        return view('admin.perfumes.reservations.show', compact('reservation'));
    }

    public function update(Request $request, PerfumeReservation $reservation)
    {
        $validated = $request->validate([
            'product_price'       => 'required|numeric|min:0',
            'deposit_amount'      => 'required|numeric|min:0',
            'product_description' => 'nullable|string',
            'expires_at'          => 'nullable|date',
            'notes'               => 'nullable|string',
        ]);

        $reservation->update($validated);

        return back()->with('success', 'Encomenda atualizada com sucesso.');
    }

    public function storePayment(StorePerfumeReservationPaymentRequest $request, PerfumeReservation $reservation)
    {
        if ($reservation->status->value !== 'active') {
            return back()->with('error', 'Não é possível adicionar pagamento a uma encomenda não ativa.');
        }

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $this->reservationService->addPayment($reservation, $data);

        return back()->with('success', 'Pagamento registrado com sucesso.');
    }

    public function destroyPayment(PerfumeReservation $reservation, PerfumeReservationPayment $payment)
    {
        if ($payment->perfume_reservation_id !== $reservation->id) {
            abort(404);
        }

        $this->reservationService->removePayment($payment);

        return back()->with('success', 'Pagamento removido com sucesso.');
    }

    public function convert(PerfumeReservation $reservation)
    {
        if ($reservation->status->value !== 'active') {
            return back()->with('error', 'Somente encomendas ativas podem ser convertidas.');
        }

        // Redireciona para criação de venda com dados da encomenda
        return redirect()->route('admin.perfumes.sales.create', [
            'from_reservation' => $reservation->id,
        ]);
    }

    public function cancel(PerfumeReservation $reservation)
    {
        if ($reservation->status->value !== 'active') {
            return back()->with('error', 'Somente encomendas ativas podem ser canceladas.');
        }

        $this->reservationService->cancel($reservation);

        return back()->with('success', 'Encomenda cancelada com sucesso.');
    }
}
