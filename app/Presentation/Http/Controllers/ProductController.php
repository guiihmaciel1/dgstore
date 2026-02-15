<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreProductRequest;
use App\Presentation\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'condition' => $request->get('condition'),
            'low_stock' => $request->boolean('low_stock'),
            'sort' => $request->get('sort', 'created_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        $products = $this->productService->list(15, $filters);

        return view('products.index', [
            'products' => $products,
            'filters' => $filters,
            'categories' => ProductCategory::cases(),
            'conditions' => ProductCondition::cases(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'categories' => ProductCategory::cases(),
            'conditions' => ProductCondition::cases(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = ProductData::fromArray($request->validated());
        $product = $this->productService->create($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function show(Product $product): View
    {
        $product->load('stockMovements.user');

        return view('products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => ProductCategory::cases(),
            'conditions' => ProductCondition::cases(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = ProductData::fromArray($request->validated());
        $this->productService->update($product, $data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Criação rápida de produto via AJAX (retorna JSON)
     */
    public function storeQuick(StoreProductRequest $request): JsonResponse
    {
        $data = ProductData::fromArray($request->validated());
        $product = $this->productService->create($data);

        return response()->json([
            'id' => $product->id,
            'name' => $product->full_name,
            'sku' => $product->sku,
            'stock' => $product->stock_quantity,
        ]);
    }

    /**
     * Busca de produtos para autocomplete (API)
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = $this->productService->search($term);

        return response()->json(
            $products->map(fn(Product $product) => [
                'id' => $product->id,
                'name' => $product->full_name,
                'sku' => $product->sku,
                'stock' => $product->stock_quantity,
            ])
        );
    }

    /**
     * Gera SKU automaticamente
     */
    public function generateSku(Request $request)
    {
        $category = $request->get('category', 'iphone');
        $model = $request->get('model', '');

        $sku = $this->productService->generateSku($category, $model);

        return response()->json(['sku' => $sku]);
    }
}
