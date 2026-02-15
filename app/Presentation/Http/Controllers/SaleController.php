<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\CancelSaleUseCase;
use App\Application\UseCases\CreateSaleUseCase;
use App\Domain\Customer\Services\CustomerService;
use App\Domain\Product\Services\ProductService;
use App\Domain\Reservation\Models\Reservation;
use App\Domain\Reservation\Services\ReservationService;
use App\Domain\Sale\DTOs\SaleData;
use App\Domain\Sale\Enums\PaymentMethod;
use App\Domain\Sale\Enums\PaymentStatus;
use App\Domain\Sale\Enums\TradeInCondition;
use App\Domain\Sale\Models\Sale;
use App\Domain\Sale\Services\SaleService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreSaleRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService,
        private readonly ProductService $productService,
        private readonly CustomerService $customerService,
        private readonly CreateSaleUseCase $createSaleUseCase,
        private readonly CancelSaleUseCase $cancelSaleUseCase,
        private readonly ReservationService $reservationService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'payment_status' => $request->get('payment_status'),
            'payment_method' => $request->get('payment_method'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'sort' => $request->get('sort', 'sold_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        $sales = $this->saleService->list(15, $filters);

        return view('sales.index', [
            'sales' => $sales,
            'filters' => $filters,
            'paymentMethods' => PaymentMethod::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $products = $this->productService->getActiveProducts();

        // Carregar reserva se vier de conversão
        $reservation = null;
        if ($request->get('from_reservation')) {
            $reservation = Reservation::with(['customer', 'product'])
                ->find($request->get('from_reservation'));
        }

        return view('sales.create', [
            'products' => $products,
            'paymentMethods' => PaymentMethod::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
            'tradeInConditions' => TradeInCondition::cases(),
            'reservation' => $reservation,
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();

            // Extrair from_reservation antes de criar SaleData
            $fromReservation = $validated['from_reservation'] ?? null;
            unset($validated['from_reservation']);

            $data = SaleData::fromArray($validated);
            $sale = $this->createSaleUseCase->execute($data);

            if ($fromReservation) {
                try {
                    $reservation = Reservation::find($fromReservation);
                    if ($reservation && $reservation->canConvert()) {
                        $this->reservationService->convert($reservation, $sale->id);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Não foi possível vincular reserva à venda: {$e->getMessage()}");
                }
            }

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', "Venda #{$sale->sale_number} realizada com sucesso!");
        } catch (\Throwable $e) {
            Log::error("Erro ao criar venda: {$e->getMessage()}", [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao finalizar venda: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale): View
    {
        $sale->load(['items.product', 'customer', 'user', 'stockMovements', 'tradeIns']);

        return view('sales.show', [
            'sale' => $sale,
        ]);
    }

    public function edit(Sale $sale): View
    {
        if ($sale->isCancelled()) {
            abort(403, 'Vendas canceladas não podem ser editadas.');
        }

        $sale->load(['items.product', 'customer', 'user', 'tradeIns']);

        return view('sales.edit', [
            'sale' => $sale,
            'paymentMethods' => PaymentMethod::cases(),
            'paymentStatuses' => PaymentStatus::cases(),
        ]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        if ($sale->isCancelled()) {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Vendas canceladas não podem ser editadas.');
        }

        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,credit_card,debit_card,pix,bank_transfer,installment'],
            'payment_status' => ['required', 'in:pending,paid,partial'],
            'installments' => ['nullable', 'integer', 'min:1', 'max:24'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'cash_payment' => ['nullable', 'numeric', 'min:0'],
            'card_payment' => ['nullable', 'numeric', 'min:0'],
            'cash_payment_method' => ['nullable', 'in:cash,pix'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $discount = (float) ($validated['discount'] ?? 0);
            $total = max(0, (float) $sale->subtotal - $discount);

            $sale->update([
                'customer_id' => !empty($validated['customer_id']) ? $validated['customer_id'] : null,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'installments' => (int) ($validated['installments'] ?? 1),
                'discount' => $discount,
                'total' => $total,
                'cash_payment' => (float) ($validated['cash_payment'] ?? 0),
                'card_payment' => (float) ($validated['card_payment'] ?? 0),
                'cash_payment_method' => $validated['cash_payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', 'Venda atualizada com sucesso!');
        } catch (\Throwable $e) {
            Log::error("Erro ao atualizar venda: {$e->getMessage()}");

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, Sale $sale): RedirectResponse
    {
        try {
            $reason = $request->get('reason');
            $this->cancelSaleUseCase->execute($sale, $reason);

            return redirect()
                ->route('sales.show', $sale)
                ->with('success', 'Venda cancelada com sucesso. O estoque foi devolvido.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Sale $sale): RedirectResponse
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,partial,cancelled'],
        ]);

        $status = PaymentStatus::from($request->payment_status);
        
        if ($status === PaymentStatus::Cancelled) {
            return $this->cancel($request, $sale);
        }

        $this->saleService->updateStatus($sale, $status);

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Status atualizado com sucesso!');
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'user']);

        $pdf = Pdf::loadView('sales.receipt', [
            'sale' => $sale,
        ]);

        return $pdf->stream("comprovante-{$sale->sale_number}.pdf");
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        if (!$sale->isCancelled()) {
            return redirect()
                ->back()
                ->with('error', 'Só é possível excluir vendas canceladas.');
        }

        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venda excluída com sucesso!');
    }
}
