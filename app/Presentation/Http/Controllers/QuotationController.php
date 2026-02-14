<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\AI\Services\GeminiService;
use App\Domain\Product\Services\ProductService;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Supplier\DTOs\QuotationData;
use App\Domain\Supplier\Models\Quotation;
use App\Domain\Supplier\Services\AiQuotationParser;
use App\Domain\Supplier\Services\QuotationImportParser;
use App\Domain\Supplier\Services\QuotationService;
use App\Domain\Supplier\Services\SupplierService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreBulkQuotationRequest;
use App\Presentation\Http\Requests\StoreImportQuotationRequest;
use App\Presentation\Http\Requests\StoreQuotationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        private readonly QuotationService $quotationService,
        private readonly SupplierService $supplierService,
        private readonly ProductService $productService,
        private readonly QuotationImportParser $importParser,
        private readonly AiQuotationParser $aiParser,
        private readonly GeminiService $geminiService,
        private readonly SaleRepositoryInterface $saleRepository,
    ) {}

    /**
     * Painel de cotações com comparativo de preços
     */
    public function index(Request $request): View
    {
        $supplierId = $request->input('supplier_id') ?: null;
        $productId = $request->input('product_id') ?: null;
        $productName = $request->input('product_name') ?: null;
        $startDate = $request->input('start_date') ?: null;
        $endDate = $request->input('end_date') ?: null;
        $perPage = (int) ($request->input('per_page') ?: 20);

        // Validar per_page: valores permitidos 10, 20 ou 0 (todos)
        if (! in_array($perPage, [10, 20, 0], true)) {
            $perPage = 20;
        }

        $quotations = $this->quotationService->list(
            perPage: $perPage === 0 ? 1000 : $perPage,
            supplierId: $supplierId,
            productId: $productId,
            productName: $productName,
            startDate: $startDate,
            endDate: $endDate
        );

        $priceComparison = $this->quotationService->getPriceComparison($productName, $supplierId);
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
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Formulário de cadastro de cotação individual
     */
    public function create(Request $request): View
    {
        $suppliers = $this->supplierService->active();
        $supplierId = $request->input('supplier_id') ?: null;

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

        $redirectTo = $request->input('redirect_to', 'quotations.index');

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

        $productsJson = $products->map(function ($p) {
            return ['id' => $p->id, 'name' => $p->name, 'price' => $p->sale_price];
        })->values();

        return view('quotations.bulk-create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'productsJson' => $productsJson,
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
        $this->quotationService->delete($quotation);

        return redirect()
            ->back()
            ->with('success', 'Cotação excluída com sucesso!');
    }

    /**
     * Excluir múltiplas cotações de uma vez
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'ulid'],
        ], [
            'ids.required' => 'Selecione pelo menos uma cotação para excluir.',
            'ids.min' => 'Selecione pelo menos uma cotação para excluir.',
        ]);

        $count = Quotation::whereIn('id', $validated['ids'])->delete();

        return redirect()
            ->back()
            ->with('success', $count . ' cotação(ões) excluída(s) com sucesso!');
    }

    /**
     * Busca de produtos para autocomplete (API)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $term = $request->input('q', '');
        
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
     * Formulário de importação de cotações
     */
    public function importForm(): View
    {
        $suppliers = $this->supplierService->active();

        return view('quotations.import', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Preview: parseia o texto e retorna itens (AJAX).
     * Lógica híbrida: regex primeiro, fallback para IA se necessário.
     */
    public function importPreview(Request $request): JsonResponse
    {
        $request->validate([
            'raw_text' => ['required', 'string', 'min:10'],
            'force_regex' => ['nullable', 'boolean'],
        ], [
            'raw_text.required' => 'Cole o texto da cotação do fornecedor.',
            'raw_text.min' => 'O texto parece muito curto para conter cotações.',
        ]);

        $rawText = $request->input('raw_text');
        $forceRegex = (bool) $request->input('force_regex', false);

        // Se forçar regex, pula IA
        if ($forceRegex) {
            $items = $this->importParser->parse($rawText);

            if (! empty($items)) {
                return response()->json([
                    'success' => true,
                    'message' => count($items) . ' itens encontrados.',
                    'items' => $items,
                    'parser_used' => 'regex',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nenhum item encontrado via regex. Tente sem forçar o modo regex.',
                'items' => [],
                'parser_used' => 'regex',
            ]);
        }

        // Padrão: IA primeiro (quando disponível)
        if ($this->aiParser->isAvailable()) {
            return $this->parseWithAi($rawText);
        }

        // Fallback para regex se IA não disponível
        $items = $this->importParser->parse($rawText);

        if (! empty($items)) {
            return response()->json([
                'success' => true,
                'message' => count($items) . ' itens encontrados (IA indisponível, usado regex).',
                'items' => $items,
                'parser_used' => 'regex',
                'is_fallback' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhum item encontrado. IA indisponível e regex não reconheceu o formato.',
            'items' => [],
            'parser_used' => 'none',
        ]);
    }

    /**
     * Parseia texto usando IA (Gemini).
     */
    private function parseWithAi(string $rawText, bool $isFallback = false): JsonResponse
    {
        if (! $this->aiParser->isAvailable()) {
            $message = $isFallback
                ? 'Nenhum item encontrado via formato padrão e a IA não está disponível. Verifique o formato do texto.'
                : 'IA não está disponível no momento. Tente o modo padrão (regex).';

            return response()->json([
                'success' => false,
                'message' => $message,
                'items' => [],
                'parser_used' => 'none',
            ]);
        }

        $items = $this->aiParser->parse($rawText);

        if (empty($items)) {
            $message = $isFallback
                ? 'Nenhum item encontrado no texto, nem via formato padrão nem via IA. Verifique o conteúdo.'
                : 'A IA não conseguiu extrair itens do texto. Verifique o conteúdo.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'items' => [],
                'parser_used' => 'ai',
            ]);
        }

        $prefix = $isFallback
            ? 'Formato não reconhecido automaticamente. A IA extraiu '
            : '';

        return response()->json([
            'success' => true,
            'message' => $prefix . count($items) . ' itens encontrados via IA — revise com atenção.',
            'items' => $items,
            'parser_used' => 'ai',
            'is_fallback' => $isFallback,
        ]);
    }

    /**
     * Salvar cotações importadas
     */
    public function importStore(StoreImportQuotationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = Auth::id();
        $supplierId = $validated['supplier_id'];
        $quotedAt = $validated['quoted_at'];
        $exchangeRate = (float) $validated['exchange_rate'];

        // Filtra apenas itens selecionados (selected = true)
        $selectedItems = collect($validated['items'])->filter(function ($item) {
            return ($item['selected'] ?? true) == true;
        });

        if ($selectedItems->isEmpty()) {
            return redirect()
                ->route('quotations.import')
                ->withInput()
                ->withErrors(['items' => 'Selecione pelo menos um item para importar.']);
        }

        $quotationsData = $selectedItems->map(function ($item) use ($userId, $supplierId, $quotedAt, $exchangeRate) {
            $priceUsd = (float) $item['price_usd'];
            $unitPriceBrl = round($priceUsd * $exchangeRate, 2);

            return QuotationData::fromArray([
                'supplier_id' => $supplierId,
                'user_id' => $userId,
                'product_name' => $item['product_name'],
                'unit_price' => $unitPriceBrl,
                'price_usd' => $priceUsd,
                'exchange_rate' => $exchangeRate,
                'quantity' => $item['quantity'] ?? 1,
                'unit' => 'un',
                'quoted_at' => $quotedAt,
                'category' => $item['category'] ?? null,
            ]);
        })->toArray();

        $this->quotationService->createMany($quotationsData);

        return redirect()
            ->route('quotations.index')
            ->with('success', count($quotationsData) . ' cotações importadas com sucesso!');
    }

    /**
     * API: Obter preços do dia para um produto
     */
    public function getPricesForProduct(Request $request): JsonResponse
    {
        $productName = $request->input('product_name') ?: null;

        if (! $productName) {
            return response()->json([]);
        }

        $prices = $this->quotationService->getLatestPricesForProduct($productName);

        return response()->json(
            $prices->map(fn ($quotation) => [
                'supplier_id' => $quotation->supplier_id,
                'supplier_name' => $quotation->supplier->name,
                'unit_price' => $quotation->unit_price,
                'formatted_price' => $quotation->formatted_unit_price,
                'quoted_at' => $quotation->quoted_at->format('d/m/Y'),
            ])
        );
    }

    /**
     * API: Análise inteligente de cotações usando IA (cache de 1h).
     */
    public function aiAnalysis(Request $request): JsonResponse
    {
        if (! $this->geminiService->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'IA não está disponível. Verifique a configuração da API key.',
            ]);
        }

        $priceComparison = $this->quotationService->getPriceComparison();

        if ($priceComparison->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Não há cotações suficientes para análise. Importe cotações primeiro.',
            ]);
        }

        // Gera hash dos dados para invalidar cache quando cotações mudam
        $dataHash = md5($priceComparison->toJson());
        $cacheKey = "ai_analysis:{$dataHash}";

        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json([
                'success' => true,
                'analysis' => $cached['analysis'],
                'products_analyzed' => $cached['products_analyzed'],
                'cached' => true,
            ]);
        }

        $analysis = $this->generateAnalysis($priceComparison);

        if ($analysis === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar análise. Tente novamente em alguns segundos.',
            ]);
        }

        // Cache por 1 hora
        Cache::put($cacheKey, [
            'analysis' => $analysis,
            'products_analyzed' => $priceComparison->count(),
        ], 3600);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'products_analyzed' => $priceComparison->count(),
            'cached' => false,
        ]);
    }

    /**
     * Gera análise de cotações via Gemini.
     */
    private function generateAnalysis($priceComparison): ?string
    {
        $quotationData = [];

        foreach ($priceComparison as $productName => $quotes) {
            $suppliers = [];

            foreach ($quotes as $quote) {
                $suppliers[] = [
                    'fornecedor' => $quote->supplier->name,
                    'preco_usd' => $quote->price_usd,
                    'preco_brl' => $quote->unit_price,
                    'data' => $quote->quoted_at->format('d/m/Y'),
                ];
            }

            $quotationData[] = [
                'produto' => $productName,
                'fornecedores' => $suppliers,
            ];
        }

        $dataJson = json_encode($quotationData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Analise os dados de cotações abaixo de uma loja de produtos Apple no Brasil.

DADOS DAS COTAÇÕES:
{$dataJson}

Forneça uma análise concisa e acionável em português brasileiro:
1. Identifique o melhor fornecedor geral (melhor custo-benefício considerando todos os produtos)
2. Destaque produtos onde há grande diferença de preço entre fornecedores (oportunidades)
3. Identifique se há produtos com preço acima do esperado para o mercado
4. Dê uma recomendação final de compra (quais produtos comprar de qual fornecedor)

Seja direto e objetivo. Use bullet points. Máximo 300 palavras.
PROMPT;

        $systemInstruction = 'Você é um consultor especializado em importação de produtos Apple para o mercado brasileiro. '
            . 'Analise dados de cotações e forneça insights práticos para o comprador da loja.';

        return $this->geminiService->generateContent($prompt, $systemInstruction);
    }

    /**
     * API: Sugestão inteligente de compra usando IA (cache de 1h).
     */
    public function aiPurchaseSuggestion(Request $request): JsonResponse
    {
        if (! $this->geminiService->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'IA não está disponível. Verifique a configuração da API key.',
            ]);
        }

        // Coleta dados do sistema
        $lowStock = $this->productService->getLowStockProducts();
        $topProducts = $this->saleRepository->getTopSellingProducts(10);
        $todayQuotations = $this->quotationService->getTodayQuotations();

        // Gera hash dos dados para invalidar cache quando dados mudam
        $dataHash = md5(
            $lowStock->pluck('id', 'stock_quantity')->toJson()
            . $todayQuotations->pluck('id')->toJson()
        );
        $cacheKey = "ai_suggestion:{$dataHash}";

        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json([
                'success' => true,
                'suggestion' => $cached['suggestion'],
                'context' => $cached['context'],
                'cached' => true,
            ]);
        }

        $suggestion = $this->generatePurchaseSuggestion($lowStock, $topProducts, $todayQuotations);

        if ($suggestion === null) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar sugestões. Tente novamente em alguns segundos.',
            ]);
        }

        $context = [
            'low_stock_count' => $lowStock->count(),
            'top_products_count' => $topProducts->count(),
            'today_quotations_count' => $todayQuotations->count(),
        ];

        // Cache por 1 hora
        Cache::put($cacheKey, [
            'suggestion' => $suggestion,
            'context' => $context,
        ], 3600);

        return response()->json([
            'success' => true,
            'suggestion' => $suggestion,
            'context' => $context,
            'cached' => false,
        ]);
    }

    /**
     * Gera sugestão de compra via Gemini.
     */
    private function generatePurchaseSuggestion($lowStock, $topProducts, $todayQuotations): ?string
    {
        $lowStockData = $lowStock->map(fn ($p) => [
            'produto' => $p->name,
            'sku' => $p->sku,
            'estoque_atual' => $p->stock_quantity,
            'alerta_minimo' => $p->min_stock_alert,
            'preco_custo' => $p->cost_price,
            'preco_venda' => $p->sale_price,
        ])->values()->toArray();

        $topProductsData = $topProducts->map(fn ($item) => [
            'produto' => $item->product?->name ?? $item->product_name ?? 'N/A',
            'quantidade_vendida' => $item->total_sold ?? 0,
        ])->values()->toArray();

        $quotationsData = $todayQuotations->map(fn ($q) => [
            'produto' => $q->product_name,
            'fornecedor' => $q->supplier->name,
            'preco_usd' => $q->price_usd,
            'preco_brl' => $q->unit_price,
        ])->values()->toArray();

        $lowStockJson = json_encode($lowStockData, JSON_UNESCAPED_UNICODE);
        $topProductsJson = json_encode($topProductsData, JSON_UNESCAPED_UNICODE);
        $quotationsJson = json_encode($quotationsData, JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
Com base nos dados abaixo de uma loja de produtos Apple, sugira quais produtos comprar e de qual fornecedor.

PRODUTOS COM ESTOQUE BAIXO:
{$lowStockJson}

PRODUTOS MAIS VENDIDOS (últimos 30 dias):
{$topProductsJson}

COTAÇÕES DISPONÍVEIS HOJE:
{$quotationsJson}

Forneça recomendações em português brasileiro:
1. URGENTE: Produtos que precisam de reposição imediata (estoque zero ou muito baixo + alta demanda)
2. IMPORTANTE: Produtos para repor em breve (estoque baixo + vendas moderadas)
3. OPORTUNIDADE: Cotações com bom preço que valem aproveitar mesmo sem urgência
4. Para cada recomendação, indique: produto, quantidade sugerida, fornecedor recomendado e motivo

Se não houver dados suficientes em alguma categoria, informe isso.
Seja direto e prático. Use bullet points. Máximo 400 palavras.
PROMPT;

        $systemInstruction = 'Você é um consultor de compras especializado em produtos Apple para varejo brasileiro. '
            . 'Analise estoque, demanda e cotações para otimizar as compras da loja. '
            . 'Priorize reposição de itens com estoque crítico e alta demanda.';

        return $this->geminiService->generateContent($prompt, $systemInstruction);
    }
}
