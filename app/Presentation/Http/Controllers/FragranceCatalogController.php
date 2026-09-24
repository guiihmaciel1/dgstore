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

        $featured = collect();
        $bestSellers = collect();
        $mostWanted = collect();
        $isFirstPageNoFilters = !$request->hasAny(['search', 'gender', 'tag'])
            && $products->currentPage() === 1;

        if ($isFirstPageNoFilters) {
            [$featured, $mostWanted, $bestSellers] = $this->loadHighlightedProducts();
        }

        return view('catalogo.index', compact(
            'products', 'tags', 'whatsappNumber', 'featured', 'bestSellers', 'mostWanted'
        ));
    }

    /**
     * Carrega os perfumes destacados em 3 seções:
     * 1. Destaques — todos em estoque (dinâmico)
     * 2. Mais Procurados — curadoria manual
     * 3. Mais Vendidos — curadoria manual
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2: \Illuminate\Support\Collection}
     */
    private function loadHighlightedProducts(): array
    {
        $mostWantedIds = [
            '76880',  // Yara
            '34696',  // Club de Nuit Intense Man
            '75805',  // Khamrah
            '72821',  // Asad
            '31861',  // Sauvage
            '65414',  // 9pm
            '46890',  // Hawas for Him
            '88175',  // Khamrah Qahwa
            '9828',   // Aventus
            '39681',  // Good Girl
            '56077',  // Libre
            '25967',  // Bleu de Chanel EDP
            '55157',  // Erba Pura
            '52802',  // Stronger With You Intensely
            '95752',  // Yara Candy
            '101124', // Asad Bourbon
            '96817',  // Hawas Black
            '94713',  // Liquid Brun
            '94697',  // Spectre Ghost
            '98689',  // Supremacy Collector's Edition
        ];

        $bestSellerIds = [
            '34696',  // Club de Nuit Intense Man
            '72821',  // Asad
            '76880',  // Yara
            '75805',  // Khamrah
            '65414',  // 9pm
            '88175',  // Khamrah Qahwa
            '46890',  // Hawas for Him
            '39681',  // Good Girl
            '14982',  // La Vie Est Belle
            '31861',  // Sauvage
            '56077',  // Libre
            '25967',  // Bleu de Chanel EDP
            '70465',  // Fakhar Black
            '70839',  // Turathi Blue
            '46093',  // 212 VIP Black
            '16657',  // Eros
            '78576',  // Good Girl Blush
            '95752',  // Yara Candy
            '27655',  // Club de Nuit Woman
            '52802',  // Stronger With You Intensely
        ];

        $allCuratedIds = array_unique(array_merge($mostWantedIds, $bestSellerIds));

        $curatedProducts = FragranceProduct::active()
            ->whereIn('fragrantica_id', $allCuratedIds)
            ->get()
            ->keyBy('fragrantica_id');

        $featured = FragranceProduct::active()
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $mostWanted = collect($mostWantedIds)
            ->map(fn (string $id) => $curatedProducts->get($id))
            ->filter();

        $bestSellers = collect($bestSellerIds)
            ->map(fn (string $id) => $curatedProducts->get($id))
            ->filter();

        return [$featured, $mostWanted, $bestSellers];
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
