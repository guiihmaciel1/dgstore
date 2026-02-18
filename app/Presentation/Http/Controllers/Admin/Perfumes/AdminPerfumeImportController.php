<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Services\AiPerfumeImportParser;
use App\Domain\Perfumes\Services\PerfumeImportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPerfumeImportController extends Controller
{
    public function __construct(
        private readonly PerfumeImportService $importService,
        private readonly AiPerfumeImportParser $aiParser,
    ) {}

    public function index(): View
    {
        return view('admin.perfumes.import');
    }

    /**
     * Preview: extrai texto do PDF e parseia (AJAX).
     * Padrão: IA primeiro, fallback para regex.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'force_regex' => ['nullable', 'boolean'],
        ]);

        $text = $this->importService->extractText($request->file('pdf_file'));
        $forceRegex = (bool) $request->input('force_regex', false);

        if ($forceRegex) {
            $items = $this->importService->parse($text);

            if (! empty($items)) {
                return response()->json([
                    'success' => true,
                    'message' => count($items) . ' produtos encontrados.',
                    'items' => $items,
                    'parser_used' => 'regex',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto encontrado via regex. Tente sem forçar o modo regex.',
                'items' => [],
                'parser_used' => 'regex',
            ]);
        }

        if ($this->aiParser->isAvailable()) {
            return $this->parseWithAi($text);
        }

        $items = $this->importService->parse($text);

        if (! empty($items)) {
            return response()->json([
                'success' => true,
                'message' => count($items) . ' produtos encontrados (IA indisponível, usado regex).',
                'items' => $items,
                'parser_used' => 'regex',
                'is_fallback' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhum produto encontrado. IA indisponível e regex não reconheceu o formato.',
            'items' => [],
            'parser_used' => 'none',
        ]);
    }

    /**
     * Salvar produtos importados.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.selected' => 'nullable|boolean',
            'items.*.name' => 'required|string|max:255',
            'items.*.brand' => 'nullable|string|max:255',
            'items.*.barcode' => 'nullable|string|max:100',
            'items.*.size_ml' => 'nullable|string|max:20',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.category' => 'required|in:masculino,feminino,unissex',
        ]);

        $selectedItems = collect($request->input('items'))
            ->filter(fn ($item) => ($item['selected'] ?? true) == true);

        if ($selectedItems->isEmpty()) {
            return redirect()->route('admin.perfumes.import')
                ->withErrors(['items' => 'Selecione pelo menos um produto para importar.']);
        }

        $created = 0;
        $updated = 0;

        foreach ($selectedItems as $item) {
            $data = [
                'name'       => $item['name'],
                'brand'      => $item['brand'] ?: null,
                'barcode'    => $item['barcode'] ?: null,
                'size_ml'    => $item['size_ml'] ?: null,
                'sale_price' => (float) $item['sale_price'],
                'cost_price' => 0,
                'category'   => $item['category'],
                'active'     => true,
            ];

            $existing = ! empty($data['barcode'])
                ? PerfumeProduct::where('barcode', $data['barcode'])->first()
                : PerfumeProduct::where('name', $data['name'])
                    ->where('brand', $data['brand'])
                    ->where('size_ml', $data['size_ml'])
                    ->first();

            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                PerfumeProduct::create($data);
                $created++;
            }
        }

        return redirect()->route('admin.perfumes.import')
            ->with('success', "Importação concluída: {$created} criados, {$updated} atualizados.");
    }

    private function parseWithAi(string $rawText): JsonResponse
    {
        $items = $this->aiParser->parse($rawText);

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'A IA não conseguiu extrair produtos do PDF. Verifique o conteúdo.',
                'items' => [],
                'parser_used' => 'ai',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($items) . ' produtos encontrados via IA — revise com atenção.',
            'items' => $items,
            'parser_used' => 'ai',
        ]);
    }
}
