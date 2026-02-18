<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Services\PerfumeImportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPerfumeImportController extends Controller
{
    private const PROGRESS_KEY = 'perfume_import_progress';

    private const PROGRESS_TTL = 300;

    public function __construct(
        private readonly PerfumeImportService $importService,
    ) {}

    public function index(): View
    {
        return view('admin.perfumes.import');
    }

    /**
     * Importação direta: extrai texto, deleta tudo, insere em lotes de 100.
     * Progresso real via Cache para polling do frontend.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $this->updateProgress('extracting', 0, 'Extraindo texto do PDF...');

            $text = $this->importService->extractText($request->file('pdf_file'));

            $this->updateProgress('parsing', 10, 'Analisando produtos...');

            $items = $this->importService->parse($text);
            $total = count($items);

            if ($total === 0) {
                $this->updateProgress('error', 100, 'Nenhum produto encontrado no PDF.');

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum produto encontrado no PDF.',
                ]);
            }

            $this->updateProgress('deleting', 15, "Removendo produtos antigos...", $total);

            PerfumeProduct::query()->forceDelete();

            $this->updateProgress('importing', 20, "Importando 0 de {$total}...", $total, 0);

            $chunks = array_chunk($items, 100);
            $processed = 0;
            $now = now();

            foreach ($chunks as $chunk) {
                $rows = array_map(fn (array $item) => [
                    'id'         => Str::ulid()->toBase32(),
                    'name'       => $item['name'],
                    'brand'      => $item['brand'] ?? null,
                    'barcode'    => $item['barcode'] ?? null,
                    'size_ml'    => $item['size_ml'] ?? null,
                    'cost_price' => $item['sale_price'] ?? 0,
                    'sale_price' => 0,
                    'category'   => $item['category'] ?? 'unissex',
                    'active'     => true,
                    'stock_quantity' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $chunk);

                PerfumeProduct::insert($rows);

                $processed += count($chunk);
                $pct = 20 + (int) (75 * $processed / $total);
                $this->updateProgress(
                    'importing',
                    $pct,
                    "Importando {$processed} de {$total}...",
                    $total,
                    $processed,
                );
            }

            $this->updateProgress('done', 100, "Importação concluída: {$total} produtos importados.", $total, $total);

            Log::info("Perfume import: {$total} produtos importados com sucesso.");

            return response()->json([
                'success' => true,
                'message' => "{$total} produtos importados com sucesso.",
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            Log::error('Perfume import error: ' . $e->getMessage());
            $this->updateProgress('error', 100, 'Erro durante a importação: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro durante a importação. Verifique os logs.',
            ], 500);
        }
    }

    /**
     * Endpoint de polling: retorna progresso atual da importação.
     */
    public function progress(): JsonResponse
    {
        $data = Cache::get(self::PROGRESS_KEY, [
            'status' => 'idle',
            'progress' => 0,
            'message' => '',
            'total' => 0,
            'processed' => 0,
        ]);

        return response()->json($data);
    }

    public function clear(): JsonResponse
    {
        try {
            $count = PerfumeProduct::count();
            PerfumeProduct::query()->forceDelete();

            Log::info("Perfume clear: {$count} produtos removidos.");

            return response()->json([
                'success' => true,
                'message' => "{$count} produtos removidos com sucesso.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Perfume clear error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar produtos.',
            ], 500);
        }
    }

    private function updateProgress(string $status, int $progress, string $message, int $total = 0, int $processed = 0): void
    {
        Cache::put(self::PROGRESS_KEY, [
            'status' => $status,
            'progress' => min($progress, 100),
            'message' => $message,
            'total' => $total,
            'processed' => $processed,
        ], self::PROGRESS_TTL);
    }
}
