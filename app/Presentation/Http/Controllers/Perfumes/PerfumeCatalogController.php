<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Perfumes;

use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Domain\Perfumes\Models\PerfumeSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfumeCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = PerfumeProduct::where('active', true);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $products = $query->orderBy('sort_order')->orderBy('name')->paginate(24)->withQueryString();
        $storeName = PerfumeSetting::get('store_name', 'DG Perfumes');

        return view('perfumes.catalog', compact('products', 'storeName'));
    }
}
