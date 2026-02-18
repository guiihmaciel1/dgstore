<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\B2B;

use App\Domain\B2B\DTOs\CreateB2BOrderDTO;
use App\Domain\B2B\Models\B2BOrder;
use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BSetting;
use App\Domain\B2B\Services\B2BOrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class B2BOrderController extends Controller
{
    public function __construct(
        private readonly B2BOrderService $orderService,
    ) {}

    public function index(): View
    {
        $retailer = Auth::guard('b2b')->user();
        $orders = $this->orderService->listForRetailer($retailer->id);

        return view('b2b.orders.index', compact('orders'));
    }

    public function show(B2BOrder $order): View|RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();

        if ($order->b2b_retailer_id !== $retailer->id) {
            abort(403);
        }

        $order->load(['items.product', 'retailer']);

        return view('b2b.orders.show', compact('order'));
    }

    public function store(Request $request): RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();
        $cart = $request->session()->get('b2b_cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Seu carrinho está vazio.');
        }

        $total = 0;
        $items = [];

        foreach ($cart as $productId => $item) {
            $product = B2BProduct::find($productId);
            if ($product) {
                $total += (float) $product->wholesale_price * $item['quantity'];
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                ];
            }
        }

        $minimumOrder = B2BSetting::getMinimumOrderAmount();
        if ($total < $minimumOrder) {
            return back()->with('error', 'O pedido mínimo é de R$ ' . number_format($minimumOrder, 2, ',', '.'));
        }

        try {
            $order = $this->orderService->createFromCart(new CreateB2BOrderDTO(
                retailerId: $retailer->id,
                items: $items,
                notes: $request->get('notes'),
            ));

            $request->session()->forget('b2b_cart');

            return redirect()->route('b2b.orders.pix', $order);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pix(B2BOrder $order): View|RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();

        if ($order->b2b_retailer_id !== $retailer->id) {
            abort(403);
        }

        if (!$order->isPendingPayment()) {
            return redirect()->route('b2b.orders.show', $order);
        }

        $order->load('items');

        return view('b2b.orders.pix', compact('order'));
    }

    public function simulatePayment(B2BOrder $order): RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();

        if ($order->b2b_retailer_id !== $retailer->id) {
            abort(403);
        }

        try {
            $this->orderService->confirmPayment($order);

            return redirect()->route('b2b.orders.show', $order)
                ->with('success', 'Pagamento PIX confirmado! Seu pedido está sendo processado.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function repeat(B2BOrder $order, Request $request): RedirectResponse
    {
        $retailer = Auth::guard('b2b')->user();

        if ($order->b2b_retailer_id !== $retailer->id) {
            abort(403);
        }

        $cart = [];

        foreach ($order->items as $item) {
            $product = B2BProduct::find($item->b2b_product_id);
            if ($product && $product->active && $product->stock_quantity > 0) {
                $qty = min($item->quantity, $product->stock_quantity);
                $cart[$product->id] = [
                    'quantity' => $qty,
                    'price' => (float) $product->wholesale_price,
                    'name' => $product->full_name,
                ];
            }
        }

        $request->session()->put('b2b_cart', $cart);

        return redirect()->route('b2b.cart')
            ->with('success', 'Itens do pedido anterior adicionados ao carrinho. Verifique as quantidades e preços atualizados.');
    }
}
