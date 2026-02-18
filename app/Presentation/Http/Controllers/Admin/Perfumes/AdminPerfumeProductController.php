<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Enums\PerfumeCategory;
use App\Domain\Perfumes\Models\PerfumeProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeProductController extends Controller
{
    public function index(Request $request)
    {
        $query = PerfumeProduct::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $products = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.perfumes.products.index', compact('products'));
    }

    public function create()
    {
        $categories = PerfumeCategory::cases();

        return view('admin.perfumes.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'brand'          => 'nullable|string|max:255',
            'category'       => 'required|in:masculino,feminino,unissex',
            'size_ml'        => 'nullable|string|max:50',
            'cost_price'     => 'required|numeric|min:0',
            'sale_price'     => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'barcode'        => 'nullable|string|max:100',
            'photo'          => 'nullable|image|max:2048',
            'active'         => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('perfume-products', 'public');
        }

        $validated['active'] = $request->boolean('active', true);

        PerfumeProduct::create($validated);

        return redirect()->route('admin.perfumes.products.index')
            ->with('success', 'Produto criado com sucesso.');
    }

    public function edit(PerfumeProduct $product)
    {
        $categories = PerfumeCategory::cases();

        return view('admin.perfumes.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, PerfumeProduct $product)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'brand'          => 'nullable|string|max:255',
            'category'       => 'required|in:masculino,feminino,unissex',
            'size_ml'        => 'nullable|string|max:50',
            'cost_price'     => 'required|numeric|min:0',
            'sale_price'     => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'barcode'        => 'nullable|string|max:100',
            'photo'          => 'nullable|image|max:2048',
            'active'         => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('perfume-products', 'public');
        }

        $validated['active'] = $request->boolean('active', true);

        $product->update($validated);

        return redirect()->route('admin.perfumes.products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(PerfumeProduct $product)
    {
        $product->delete();

        return redirect()->route('admin.perfumes.products.index')
            ->with('success', 'Produto removido com sucesso.');
    }
}
