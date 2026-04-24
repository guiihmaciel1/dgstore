<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreProductRequest;
use App\Presentation\Http\Requests\UpdateProductRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            'status' => $request->get('status'),
            'in_stock' => $request->has('in_stock') ? $request->boolean('in_stock') : !$request->hasAny(['search', 'category', 'condition', 'status', 'low_stock']),
            'low_stock' => $request->boolean('low_stock'),
            'sort' => $request->get('sort', 'created_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        if ($filters['status'] === 'active') {
            $filters['active'] = true;
        } elseif ($filters['status'] === 'inactive') {
            $filters['active'] = false;
        }

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

        if ($checklistId = $request->validated('checklist_id')) {
            $product->update(['checklist_id' => $checklistId]);
        }

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function show(Product $product): View
    {
        $product->load(['stockMovements.user', 'tradeIn.sale.customer', 'saleItems.sale.customer']);

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

        if ($checklistId = $request->validated('checklist_id')) {
            $product->update(['checklist_id' => $checklistId]);
        }

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
        $products->load('tradeIn:id,product_id,sale_id,estimated_value');

        $canViewFinancials = auth()->user()->canViewFinancials();

        $results = $products->map(fn(Product $product) => [
            'id' => $product->id,
            'name' => $product->full_name,
            'sku' => $product->sku,
            'stock' => $product->stock_quantity,
            'cost_price' => $canViewFinancials && $product->cost_price ? (float) $product->cost_price : null,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'condition' => $product->condition?->value,
            'from_trade_in' => $product->tradeIn !== null,
            'is_consignment' => false,
            'consignment_item_id' => null,
        ]);

        $consignmentItems = ConsignmentStockItem::with('supplier')
            ->available()
            ->where('available_quantity', '>', 0)
            ->search($term)
            ->limit(10)
            ->get();

        $consignmentResults = $consignmentItems->map(fn(ConsignmentStockItem $item) => [
            'id' => $item->id,
            'name' => $item->full_name . ' [' . $item->supplier->name . ']',
            'sku' => $item->imei ?? '-',
            'stock' => $item->available_quantity,
            'cost_price' => $canViewFinancials ? (float) $item->supplier_cost : null,
            'condition' => 'new',
            'from_trade_in' => false,
            'is_consignment' => true,
            'consignment_item_id' => $item->id,
            'suggested_price' => $item->suggested_price ? (float) $item->suggested_price : null,
        ]);

        return response()->json($results->concat($consignmentResults)->values());
    }

    /**
     * Gera SKU automaticamente
     */
    public function generateSku(Request $request)
    {
        $category = $request->get('category', 'iphone') ?? 'iphone';
        $model = $request->get('model') ?? '';

        $sku = $this->productService->generateSku($category, $model);

        return response()->json(['sku' => $sku]);
    }

    public function label(Product $product): Response
    {
        $pdf = Pdf::loadView('products.label', ['products' => collect([$product])])
            ->setPaper([0, 0, 198.43, 141.73]); // 7cm x 5cm

        return $pdf->stream("etiqueta-{$product->sku}.pdf");
    }

    public function labelBatch(Request $request): Response|RedirectResponse
    {
        $query = Product::where('active', true);

        if ($request->filled('ids')) {
            $query->whereIn('id', $request->input('ids'));
        } else {
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('color', 'like', "%{$search}%");
                });
            }
            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }
            if ($request->filled('condition')) {
                $query->where('condition', $request->input('condition'));
            }
            $query->where('stock_quantity', '>', 0);
        }

        $products = $query->orderBy('name')->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Nenhum produto encontrado para gerar etiquetas.');
        }

        $pdf = Pdf::loadView('products.label', ['products' => $products])
            ->setPaper([0, 0, 198.43, 141.73]);

        return $pdf->stream('etiquetas-lote.pdf');
    }
}
