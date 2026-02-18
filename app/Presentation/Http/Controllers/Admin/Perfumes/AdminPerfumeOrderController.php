<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Enums\PerfumeOrderStatus;
use App\Domain\Perfumes\Models\PerfumeOrder;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeRetailer;
use App\Domain\Perfumes\Services\PerfumeOrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeOrderController extends Controller
{
    public function __construct(
        private readonly PerfumeOrderService $orderService,
    ) {}

    public function index(Request $request)
    {
        $query = PerfumeOrder::with('retailer');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('retailer', fn ($r) => $r->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.perfumes.orders.index', compact('orders'));
    }

    public function create()
    {
        $retailers = PerfumeRetailer::where('status', 'active')->orderBy('name')->get();
        $products = PerfumeProduct::where('active', true)->where('stock_quantity', '>', 0)->orderBy('name')->get();

        return view('admin.perfumes.orders.create', compact('retailers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perfume_retailer_id' => 'required|exists:perfume_retailers,id',
            'payment_method'      => 'required|in:pix,consignment',
            'discount'            => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:1000',
            'items'               => 'required|array|min:1',
            'items.*.perfume_product_id' => 'required|exists:perfume_products,id',
            'items.*.quantity'           => 'required|integer|min:1',
        ]);

        $order = $this->orderService->create(
            $request->only(['perfume_retailer_id', 'payment_method', 'discount', 'notes']),
            $request->input('items'),
        );

        $retailer = $order->retailer;
        $message = $this->orderService->buildWhatsAppMessage($order);
        $whatsappNumber = preg_replace('/\D/', '', $retailer->whatsapp);
        $whatsappLink = "https://wa.me/55{$whatsappNumber}?text=" . urlencode($message);

        return redirect()->route('admin.perfumes.orders.show', $order)
            ->with('success', "Pedido {$order->order_number} criado com sucesso.")
            ->with('whatsapp_link', $whatsappLink)
            ->with('whatsapp_retailer', $retailer->name);
    }

    public function show(PerfumeOrder $order)
    {
        $order->load(['retailer', 'items.product', 'payments']);

        return view('admin.perfumes.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, PerfumeOrder $order)
    {
        $request->validate([
            'status' => 'required|in:received,separating,shipped,delivered,cancelled',
        ]);

        $status = PerfumeOrderStatus::from($request->input('status'));
        $this->orderService->updateStatus($order, $status);

        $retailer = $order->retailer;
        $message = $this->orderService->buildWhatsAppMessage($order->fresh());
        $whatsappNumber = preg_replace('/\D/', '', $retailer->whatsapp);
        $whatsappLink = "https://wa.me/55{$whatsappNumber}?text=" . urlencode($message);

        return redirect()->route('admin.perfumes.orders.show', $order)
            ->with('success', "Status atualizado para: {$status->label()}")
            ->with('whatsapp_link', $whatsappLink)
            ->with('whatsapp_retailer', $retailer->name);
    }
}
