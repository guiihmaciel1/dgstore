<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Product\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Tabela de preços rápida.
     * Filtra dados sensíveis (custo/margem) para não-admins.
     */
    public function priceTable(): View
    {
        $products = Product::where('active', true)->orderBy('name')->get();
        $isAdmin = auth()->user()->isAdmin();

        $productsJson = $products->map(function ($p) use ($isAdmin) {
            $data = [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category->value,
                'storage' => $p->storage,
                'condition' => $p->condition->value,
                'sale_price' => (float) $p->sale_price,
                'stock' => $p->stock_quantity,
            ];

            if ($isAdmin) {
                $data['cost_price'] = (float) $p->cost_price;
                $data['margin'] = $p->profit_margin;
            }

            return $data;
        })->values();

        return view('tools.price-table', ['productsJson' => $productsJson]);
    }
}
