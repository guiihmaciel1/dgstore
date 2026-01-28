<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Domain\Stock\Enums\StockMovementType;
use App\Domain\Stock\Services\StockService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreStockMovementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $movements = $this->stockService->getRecentMovements(30, 100);

        return view('stock.index', [
            'movements' => $movements,
        ]);
    }

    public function alerts(): View
    {
        $products = $this->stockService->getLowStockProducts();

        return view('stock.alerts', [
            'products' => $products,
        ]);
    }

    public function create(): View
    {
        $products = $this->productService->getActiveProducts();

        return view('stock.create', [
            'products' => $products,
            'types' => [
                StockMovementType::In,
                StockMovementType::Adjustment,
            ],
        ]);
    }

    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);
        $userId = auth()->id();

        if ($validated['type'] === 'in') {
            $this->stockService->registerEntry(
                $product,
                $validated['quantity'],
                $userId,
                $validated['reason'] ?? null
            );
            $message = "Entrada de {$validated['quantity']} unidades registrada com sucesso!";
        } else {
            $newQuantity = $validated['quantity'];
            $this->stockService->registerAdjustment(
                $product,
                $newQuantity,
                $userId,
                $validated['reason'] ?? null
            );
            $message = "Ajuste de estoque para {$newQuantity} unidades registrado com sucesso!";
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', $message);
    }

    public function productHistory(Product $product): View
    {
        $movements = $this->stockService->getProductHistory($product->id);

        return view('stock.history', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }
}
