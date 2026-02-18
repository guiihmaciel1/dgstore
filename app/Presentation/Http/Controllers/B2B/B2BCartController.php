<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\B2B;

use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2BCartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('b2b_cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = B2BProduct::find($productId);
            if ($product && $product->active && $product->stock_quantity > 0) {
                $subtotal = (float) $product->wholesale_price * $item['quantity'];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        $minimumOrder = B2BSetting::getMinimumOrderAmount();

        return view('b2b.cart.index', compact('cartItems', 'total', 'minimumOrder'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:b2b_products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = B2BProduct::findOrFail($validated['product_id']);

        if (!$product->active || $product->stock_quantity <= 0) {
            return back()->with('error', 'Produto indisponível.');
        }

        $cart = $request->session()->get('b2b_cart', []);
        $currentQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $currentQty + $validated['quantity'];

        if ($newQty > $product->stock_quantity) {
            return back()->with('error', "Estoque insuficiente. Disponível: {$product->stock_quantity}");
        }

        $cart[$product->id] = [
            'quantity' => $newQty,
            'price' => (float) $product->wholesale_price,
            'name' => $product->full_name,
        ];

        $request->session()->put('b2b_cart', $cart);

        return back()->with('success', "{$product->full_name} adicionado ao carrinho.");
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:b2b_products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = B2BProduct::findOrFail($validated['product_id']);
        $cart = $request->session()->get('b2b_cart', []);

        if (!isset($cart[$product->id])) {
            return back()->with('error', 'Produto não encontrado no carrinho.');
        }

        if ($validated['quantity'] > $product->stock_quantity) {
            return back()->with('error', "Estoque insuficiente. Disponível: {$product->stock_quantity}");
        }

        $cart[$product->id]['quantity'] = $validated['quantity'];
        $request->session()->put('b2b_cart', $cart);

        return back()->with('success', 'Carrinho atualizado.');
    }

    public function remove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required'],
        ]);

        $cart = $request->session()->get('b2b_cart', []);
        unset($cart[$validated['product_id']]);
        $request->session()->put('b2b_cart', $cart);

        return back()->with('success', 'Item removido do carrinho.');
    }
}
