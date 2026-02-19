<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Enums\PerfumeSalePaymentStatus;
use App\Domain\Perfumes\Models\PerfumeCustomer;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeReservation;
use App\Domain\Perfumes\Models\PerfumeSale;
use App\Domain\Perfumes\Services\PerfumeSaleService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Perfumes\StorePerfumeSaleRequest;
use Illuminate\Http\Request;

class AdminPerfumeSaleController extends Controller
{
    public function __construct(
        private PerfumeSaleService $saleService
    ) {}

    public function index(Request $request)
    {
        $query = PerfumeSale::with(['customer', 'user']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        if ($method = $request->get('payment_method')) {
            $query->where('payment_method', $method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }

        $sales = $query->latest('sold_at')->paginate(20)->withQueryString();

        // Estatísticas
        $stats = [
            'total'  => PerfumeSale::sum('total'),
            'count'  => PerfumeSale::count(),
            'profit' => PerfumeSale::get()->sum('profit'),
            'today'  => PerfumeSale::today()->sum('total'),
        ];

        return view('admin.perfumes.sales.index', compact('sales', 'stats'));
    }

    public function create(Request $request)
    {
        $customers = PerfumeCustomer::orderBy('name')->get();
        $products = PerfumeProduct::where('active', true)
            ->orderBy('stock_quantity', 'desc')
            ->orderBy('name')
            ->get();

        $reservation = null;
        if ($request->filled('from_reservation')) {
            $reservation = PerfumeReservation::with(['customer', 'product'])->findOrFail($request->from_reservation);
        }

        return view('admin.perfumes.sales.create', compact('customers', 'products', 'reservation'));
    }

    public function store(StorePerfumeSaleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['sold_at'] = now();

            // Calcula subtotal e total
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = PerfumeProduct::find($item['perfume_product_id']);
                $unitPrice = $item['unit_price'] ?? $product->sale_price;
                $subtotal += $unitPrice * $item['quantity'];
            }
            $total = $subtotal - ($data['discount'] ?? 0);

            // Define payment_amount como o total se não foi fornecido
            if (!isset($data['payment_amount']) || $data['payment_amount'] === null) {
                $data['payment_amount'] = $total;
            }

            // Calcula o status de pagamento
            if (!isset($data['payment_status'])) {
                $data['payment_status'] = ($data['payment_amount'] >= $total) 
                    ? PerfumeSalePaymentStatus::Paid 
                    : PerfumeSalePaymentStatus::Partial;
            }

            $items = $data['items'];
            unset($data['items']);

            $sale = $this->saleService->create($data, $items);

            return redirect()->route('admin.perfumes.sales.show', $sale)
                ->with('success', 'Venda registrada com sucesso.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(PerfumeSale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'originReservation']);

        // Análise de lucro
        $totalCost = $sale->items->sum(fn($item) => $item->cost_price * $item->quantity);
        $revenue = $sale->total;
        $profit = $revenue - $totalCost;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        $profitAnalysis = [
            'total_cost' => $totalCost,
            'revenue'    => $revenue,
            'profit'     => $profit,
            'margin'     => $margin,
        ];

        return view('admin.perfumes.sales.show', compact('sale', 'profitAnalysis'));
    }

    public function cancel(PerfumeSale $sale)
    {
        if ($sale->trashed()) {
            return back()->with('error', 'Esta venda já foi cancelada.');
        }

        try {
            $this->saleService->cancel($sale);

            return redirect()->route('admin.perfumes.sales.index')
                ->with('success', 'Venda cancelada com sucesso. O estoque foi estornado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
