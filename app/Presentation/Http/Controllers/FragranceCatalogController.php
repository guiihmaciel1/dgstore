<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Fragrance\Models\FragranceProduct;
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

        $products = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(24)
            ->withQueryString();

        $whatsappNumber = PerfumeSetting::get('whatsapp_number', '');

        return view('catalogo.index', compact('products', 'whatsappNumber'));
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
