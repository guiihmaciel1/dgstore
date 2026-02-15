<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Tabela de produtos com informações de estoque.
     * Preços foram removidos do produto (agora são definidos por venda).
     */
    public function priceTable(): View
    {
        $products = Product::where('active', true)->orderBy('name')->get();

        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category->value,
                'storage' => $p->storage,
                'condition' => $p->condition->value,
                'stock' => $p->stock_quantity,
            ];
        })->values();

        return view('tools.price-table', ['productsJson' => $productsJson]);
    }
}
