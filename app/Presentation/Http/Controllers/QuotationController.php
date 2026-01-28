<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Services\ProductService;
use App\Domain\Supplier\DTOs\QuotationData;
use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Services\QuotationService;
use App\Domain\Supplier\Services\SupplierService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreBulkQuotationRequest;
use App\Presentation\Http\Requests\StoreQuotationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        private readonly QuotationService $quotationService,
        private readonly SupplierService $supplierService,
        private readonly ProductService $productService
    ) {}

    /**
     * Painel de cotações com comparativo de preços
     */
    public function index(Request $request): View
    {
        $supplierId = $request->get('supplier_id');
        $productId = $request->get('product_id');
        $productName = $request->get('product_name');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $quotations = $this->quotationService->list(
            perPage: 20,
            supplierId: $supplierId,
            productId: $productId,
            productName: $productName,
            startDate: $startDate,
            endDate: $endDate
        );

        $priceComparison = $this->quotationService->getPriceComparison($productName);
        $suppliers = $this->supplierService->active();
        $productNames = $this->quotationService->getUniqueProductNames();
        $todayQuotations = $this->quotationService->getTodayQuotations();

        return view('quotations.index', [
            'quotations' => $quotations,
            'priceComparison' => $priceComparison,
            'suppliers' => $suppliers,
            'productNames' => $productNames,
            'todayQuotations' => $todayQuotations,
            'filters' => [
                'supplier_id' => $supplierId,
                'product_id' => $productId,
                'product_name' => $productName,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Formulário de cadastro de cotação individual
     */
    public function create(Request $request): View
    {
        $suppliers = $this->supplierService->active();
        $supplierId = $request->get('supplier_id');

        return view('quotations.create', [
            'suppliers' => $suppliers,
            'selectedSupplierId' => $supplierId,
        ]);
    }

    /**
     * Salvar cotação individual
     */
    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $data = QuotationData::fromArray($validated);
        $quotation = $this->quotationService->create($data);

        $redirectTo = $request->get('redirect_to', 'quotations.index');

        if ($redirectTo === 'supplier') {
            return redirect()
                ->route('suppliers.show', $quotation->supplier_id)
                ->with('success', 'Cotação cadastrada com sucesso!');
        }

        return redirect()
            ->route('quotations.index')
            ->with('success', 'Cotação cadastrada com sucesso!');
    }

    /**
     * Formulário de cadastro rápido (múltiplas cotações)
     */
    public function bulkCreate(): View
    {
        $suppliers = $this->supplierService->active();
        $products = $this->productService->active();

        return view('quotations.bulk-create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    /**
     * Salvar múltiplas cotações de uma vez
     */
    public function bulkStore(StoreBulkQuotationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = Auth::id();
        $supplierId = $validated['supplier_id'];
        $quotedAt = $validated['quoted_at'];

        $quotationsData = collect($validated['quotations'])->map(function ($item) use ($userId, $supplierId, $quotedAt) {
            return QuotationData::fromArray([
                'supplier_id' => $supplierId,
                'user_id' => $userId,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'] ?? 1,
                'unit' => $item['unit'] ?? 'un',
                'quoted_at' => $quotedAt,
                'notes' => $item['notes'] ?? null,
            ]);
        })->toArray();

        $this->quotationService->createMany($quotationsData);

        return redirect()
            ->route('quotations.index')
            ->with('success', count($quotationsData) . ' cotações cadastradas com sucesso!');
    }

    /**
     * Excluir cotação
     */
    public function destroy(Quotation $quotation): RedirectResponse
    {
        $supplierId = $quotation->supplier_id;
        $this->quotationService->delete($quotation);

        return redirect()
            ->back()
            ->with('success', 'Cotação excluída com sucesso!');
    }

    /**
     * Busca de produtos para autocomplete (API)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = $this->productService->search($term);

        return response()->json(
            $products->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->full_name,
                'sku' => $product->sku,
                'sale_price' => $product->sale_price,
            ])
        );
    }

    /**
     * API: Obter preços do dia para um produto
     */
    public function getPricesForProduct(Request $request): JsonResponse
    {
        $productName = $request->get('product_name');
        
        if (!$productName) {
            return response()->json([]);
        }

        $prices = $this->quotationService->getLatestPricesForProduct($productName);

        return response()->json(
            $prices->map(fn($quotation) => [
                'supplier_id' => $quotation->supplier_id,
                'supplier_name' => $quotation->supplier->name,
                'unit_price' => $quotation->unit_price,
                'formatted_price' => $quotation->formatted_unit_price,
                'quoted_at' => $quotation->quoted_at->format('d/m/Y'),
            ])
        );
    }
}
