<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Fragrance\Models\FragranceProduct;
use App\Domain\Fragrance\Models\FragranceTag;
use App\Domain\Perfumes\Models\PerfumeSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FragranceCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = FragranceProduct::with('accords')->active();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($gender = $request->get('gender')) {
            $query->byGender($gender);
        }

        if ($tag = $request->get('tag')) {
            $query->byTag($tag);
        }

        $products = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $tags = FragranceTag::active()->ordered()->withCount([
            'products' => fn ($q) => $q->where('active', true),
        ])->get();

        $whatsappNumber = PerfumeSetting::get('whatsapp_number', '');

        $bestSellers = collect();
        $mostWanted = collect();
        $isFirstPageNoFilters = !$request->hasAny(['search', 'gender', 'tag'])
            && $products->currentPage() === 1;

        if ($isFirstPageNoFilters) {
            [$bestSellers, $mostWanted] = $this->loadHighlightedProducts();
        }

        return view('catalogo.index', compact(
            'products', 'tags', 'whatsappNumber', 'bestSellers', 'mostWanted'
        ));
    }

    /**
     * Carrega os perfumes destacados (mais vendidos e mais procurados)
     * usando fragrantica_id para manter a lista estável.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     */
    private function loadHighlightedProducts(): array
    {
        $bestSellerIds = [
            '76880',  // Yara
            '65414',  // 9pm
            '72821',  // Asad
            '34696',  // Club de Nuit Intense Man
            '64579',  // Sabah Al Ward
            '98689',  // Supremacy Collector's Edition
            '95752',  // Yara Candy
            '94713',  // Liquid Brun
            '117616', // Asad Elixir
            '96817',  // Hawas Black
        ];

        $mostWantedIds = [
            '70839',  // Turathi Blue
            '78475',  // Club de Nuit Blue Iconic
            '81376',  // Ameerat Al Arab
            '93628',  // Eclaire
            '70466',  // Fakhar Rose
            '117615', // Yara Elixir
            '109599', // Delilah Blanc
            '69362',  // Royal Amber
            '109709', // Pacific Aura
            '55157',  // Erba Pura
            '52802',  // Stronger With You Intensely
            '81642',  // Le Male Elixir
        ];

        $allIds = array_merge($bestSellerIds, $mostWantedIds);

        $products = FragranceProduct::active()
            ->whereIn('fragrantica_id', $allIds)
            ->get()
            ->keyBy('fragrantica_id');

        $bestSellers = collect($bestSellerIds)
            ->map(fn (string $id) => $products->get($id))
            ->filter();

        $mostWanted = collect($mostWantedIds)
            ->map(fn (string $id) => $products->get($id))
            ->filter();

        return [$bestSellers, $mostWanted];
    }

    public function show(string $slug): View
    {
        $fragrance = FragranceProduct::with(['accords', 'notes'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $whatsappNumber = PerfumeSetting::get('whatsapp_number', '');

        $related = FragranceProduct::active()
            ->where('id', '!=', $fragrance->id)
            ->where(function ($q) use ($fragrance) {
                $q->where('brand', $fragrance->brand)
                  ->orWhere('gender', $fragrance->gender->value);
            })
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('catalogo.show', compact('fragrance', 'whatsappNumber', 'related'));
    }
}
