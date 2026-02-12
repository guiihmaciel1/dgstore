<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\DTOs\ProductData;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Domain\Sale\Enums\TradeInCondition;
use App\Domain\Sale\Enums\TradeInStatus;
use App\Domain\Sale\Models\TradeIn;
use App\Domain\Stock\Enums\StockMovementType;
use App\Domain\Stock\Services\StockService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreStockMovementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $productsJson = $products->map(function ($p) {
            return [
                'id'    => $p->id,
                'name'  => $p->name,
                'sku'   => $p->sku,
                'stock' => $p->stock_quantity,
            ];
        })->values();

        return view('stock.create', [
            'products' => $products,
            'productsJson' => $productsJson,
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

        $quantity = (int) $validated['quantity'];

        if ($validated['type'] === 'in') {
            $this->stockService->registerEntry(
                $product,
                $quantity,
                $userId,
                $validated['reason'] ?? null
            );
            $message = "Entrada de {$quantity} unidades registrada com sucesso!";
        } else {
            $this->stockService->registerAdjustment(
                $product,
                $quantity,
                $userId,
                $validated['reason'] ?? null
            );
            $message = "Ajuste de estoque para {$quantity} unidades registrado com sucesso!";
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

    public function tradeIns(Request $request): View
    {
        $status = $request->get('status', 'pending');
        
        $query = TradeIn::with(['sale', 'sale.customer', 'product'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $tradeIns = $query->paginate(15)->withQueryString();

        // Contagens consolidadas em uma única query
        $rawStats = TradeIn::selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'processed' THEN 1 ELSE 0 END) as processed,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        ")->first();

        $stats = [
            'pending' => (int) ($rawStats->pending ?? 0),
            'processed' => (int) ($rawStats->processed ?? 0),
            'rejected' => (int) ($rawStats->rejected ?? 0),
        ];

        return view('stock.trade-ins', [
            'tradeIns' => $tradeIns,
            'stats' => $stats,
            'currentStatus' => $status,
        ]);
    }

    public function processTradeIn(Request $request, TradeIn $tradeIn): RedirectResponse
    {
        if (!$tradeIn->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Este trade-in já foi processado ou rejeitado.');
        }

        $request->validate([
            'action' => 'required|in:reject,create',
        ]);

        $action = $request->get('action');

        if ($action === 'reject') {
            $tradeIn->markAsRejected();
            return redirect()
                ->back()
                ->with('success', 'Trade-in rejeitado com sucesso.');
        }

        // Cria produto automaticamente a partir do trade-in
        try {
            $product = DB::transaction(function () use ($tradeIn) {
                $condition = match ($tradeIn->condition) {
                    TradeInCondition::Excellent => ProductCondition::Used,
                    default => ProductCondition::Used,
                };

                $sku = $this->productService->generateSku('iphone', $tradeIn->device_model ?? '');

                $productData = ProductData::fromArray([
                    'name' => $tradeIn->device_name,
                    'sku' => $sku,
                    'category' => ProductCategory::Smartphone->value,
                    'cost_price' => $tradeIn->estimated_value,
                    'sale_price' => 0, // Definir preço de venda manualmente depois
                    'model' => $tradeIn->device_model,
                    'condition' => $condition->value,
                    'imei' => $tradeIn->imei,
                    'stock_quantity' => 1,
                    'notes' => "Origem: Trade-in da venda #{$tradeIn->sale?->sale_number}. {$tradeIn->notes}",
                ]);

                $product = $this->productService->create($productData);

                // Registra entrada no estoque
                $this->stockService->registerEntry(
                    $product,
                    1,
                    auth()->id(),
                    "Trade-in processado (venda #{$tradeIn->sale?->sale_number})"
                );

                // Marca como processado e vincula ao produto
                $tradeIn->markAsProcessed($product->id);

                return $product;
            });

            return redirect()
                ->route('products.show', $product)
                ->with('success', "Produto criado automaticamente a partir do trade-in: {$product->full_name}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', "Erro ao processar trade-in: {$e->getMessage()}");
        }
    }

    public function linkTradeInToProduct(Request $request, TradeIn $tradeIn): RedirectResponse
    {
        if (!$tradeIn->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Este trade-in já foi processado ou rejeitado.');
        }

        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $tradeIn->markAsProcessed($request->product_id);

        return redirect()
            ->route('stock.trade-ins')
            ->with('success', 'Trade-in vinculado ao produto com sucesso!');
    }
}
