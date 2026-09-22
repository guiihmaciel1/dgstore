<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Fragrance\Models\FragranceProduct;
use App\Domain\Fragrance\Services\FragranticaScraperService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\UpdateFragranceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FragranceController extends Controller
{
    public function __construct(
        private readonly FragranticaScraperService $scraper,
    ) {}

    public function index(Request $request): View
    {
        $query = FragranceProduct::with('accords');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($gender = $request->get('gender')) {
            $query->where('gender', $gender);
        }

        if ($request->get('active') !== null && $request->get('active') !== '') {
            $query->where('active', (bool) $request->get('active'));
        }

        $sort = $request->get('sort', 'created_at');
        $dir = $request->get('dir', 'desc');
        $query->orderBy($sort, $dir);

        $fragrances = $query->paginate(20)->withQueryString();

        return view('fragrances.index', compact('fragrances'));
    }

    public function create(): View
    {
        return view('fragrances.create');
    }

    /**
     * Importa perfume a partir da URL + HTML colado pelo usuário.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'url'  => ['required', 'url', 'regex:#^https?://(www\.)?fragrantica\.com(\.\w+)?/perfume/.+-\d+\.html#i'],
            'html' => ['required', 'string', 'min:500'],
        ], [
            'url.required'  => 'Informe a URL do perfume no Fragrantica.',
            'url.regex'     => 'A URL deve ser do Fragrantica.',
            'html.required' => 'Cole o código-fonte da página do Fragrantica.',
            'html.min'      => 'O conteúdo colado parece incompleto. Use Ctrl+U na página do perfume.',
        ]);

        try {
            $product = $this->scraper->importFromHtml($request->input('url'), $request->input('html'));

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Perfume \"{$product->name}\" importado com sucesso!",
                    'redirect' => route('fragrances.edit', $product),
                ]);
            }

            return redirect()
                ->route('fragrances.edit', $product)
                ->with('success', "Perfume \"{$product->name}\" importado com sucesso! Defina o preço e estoque.");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('error', "Erro ao importar: {$e->getMessage()}");
        }
    }

    public function edit(FragranceProduct $fragrance): View
    {
        $fragrance->load(['accords', 'notes']);

        return view('fragrances.edit', compact('fragrance'));
    }

    public function update(UpdateFragranceRequest $request, FragranceProduct $fragrance): RedirectResponse
    {
        $data = $request->validated();

        $pixPrice = (float) ($data['pix_price'] ?? 0);
        $discountPercent = (int) ($data['pix_discount_percent'] ?? 10);

        if ($pixPrice > 0) {
            $data['sale_price'] = FragranceProduct::calculateInstallmentPrice($pixPrice, $discountPercent);
        } else {
            $data['sale_price'] = null;
        }

        $fragrance->update($data);

        return redirect()
            ->route('fragrances.edit', $fragrance)
            ->with('success', 'Perfume atualizado com sucesso!');
    }

    /**
     * Re-importa dados via HTML do browser.
     */
    public function rescrape(Request $request, FragranceProduct $fragrance): JsonResponse|RedirectResponse
    {
        $html = $request->input('html');

        try {
            if ($html && mb_strlen($html) > 500) {
                $this->scraper->rescrapeFromHtml($fragrance, $html);
            } else {
                $this->scraper->rescrape($fragrance);
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Dados reimportados com sucesso!']);
            }

            return redirect()
                ->route('fragrances.edit', $fragrance)
                ->with('success', 'Dados reimportados do Fragrantica com sucesso!');
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', "Erro ao reimportar: {$e->getMessage()}");
        }
    }

    public function destroy(FragranceProduct $fragrance): RedirectResponse
    {
        $name = $fragrance->name;
        $fragrance->delete();

        return redirect()
            ->route('fragrances.index')
            ->with('success', "Perfume \"{$name}\" removido com sucesso.");
    }
}
