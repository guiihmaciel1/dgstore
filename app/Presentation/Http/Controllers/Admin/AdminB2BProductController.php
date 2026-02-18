<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin;

use App\Domain\B2B\DTOs\CreateB2BProductDTO;
use App\Domain\B2B\Enums\B2BProductCondition;
use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BSetting;
use App\Domain\B2B\Services\B2BProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminB2BProductController extends Controller
{
    public function __construct(
        private readonly B2BProductService $productService,
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->listForAdmin(
            search: $request->get('search'),
            condition: $request->get('condition'),
        );

        $conditions = B2BProductCondition::cases();
        $lowStockThreshold = B2BSetting::getLowStockThreshold();

        return view('admin.b2b.products.index', compact('products', 'conditions', 'lowStockThreshold'));
    }

    public function create(): View
    {
        $conditions = B2BProductCondition::cases();

        return view('admin.b2b.products.create', compact('conditions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', 'in:sealed,semi_new'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $fileName = uniqid('b2b_') . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move(public_path('images/b2b-products'), $fileName);
            $photoPath = 'images/b2b-products/' . $fileName;
        }

        $this->productService->create(CreateB2BProductDTO::fromArray(array_merge($validated, [
            'photo' => $photoPath,
        ])));

        return redirect()->route('admin.b2b.products.index')
            ->with('success', 'Produto B2B cadastrado com sucesso.');
    }

    public function edit(B2BProduct $product): View
    {
        $conditions = B2BProductCondition::cases();

        return view('admin.b2b.products.edit', compact('product', 'conditions'));
    }

    public function update(Request $request, B2BProduct $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'condition' => ['required', 'in:sealed,semi_new'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'active' => ['nullable'],
        ]);

        $validated['active'] = $request->has('active');

        $this->productService->update($product, $validated);

        return redirect()->route('admin.b2b.products.index')
            ->with('success', 'Produto B2B atualizado com sucesso.');
    }

    public function destroy(B2BProduct $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('admin.b2b.products.index')
            ->with('success', 'Produto B2B removido com sucesso.');
    }
}
