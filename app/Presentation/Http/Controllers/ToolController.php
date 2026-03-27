<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Marketing\Models\MarketingPrice;
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

    public function stoneCalculator(): View
    {
        $prices = MarketingPrice::active()->ordered()->get();

        $grouped = $prices->groupBy(function ($p) {
            return trim($p->name . ' ' . ($p->storage ?? ''));
        });

        $quickValues = $grouped->map(function ($items, $key) {
            if ($items->count() === 1 && (! $items->first()->color || $items->first()->color === 'Todas')) {
                return [
                    'name' => $key,
                    'value' => (float) $items->first()->price,
                ];
            }

            $colorMap = [
                'preto' => '#1f2937', 'black' => '#1f2937', 'desert' => '#1f2937',
                'branco' => '#f5f5f4', 'white' => '#f5f5f4', 'starlight' => '#f5f5f4',
                'azul' => '#3b82f6', 'blue' => '#3b82f6', 'ultramarine' => '#3b82f6', 'teal' => '#0d9488',
                'verde' => '#22c55e', 'green' => '#22c55e',
                'rosa' => '#ec4899', 'pink' => '#ec4899',
                'roxo' => '#8b5cf6', 'purple' => '#8b5cf6',
                'vermelho' => '#ef4444', 'red' => '#ef4444',
                'laranja' => '#f97316', 'orange' => '#f97316',
                'amarelo' => '#eab308', 'yellow' => '#eab308',
                'dourado' => '#d4a574', 'gold' => '#d4a574',
                'prata' => '#9ca3af', 'silver' => '#9ca3af',
                'cinza' => '#6b7280', 'gray' => '#6b7280', 'graphite' => '#6b7280',
                'natural' => '#c2a67d', 'titanium' => '#8b8589',
            ];

            $variants = $items->map(function ($item) use ($colorMap) {
                $label = $item->color ?? 'Padrão';
                $colorLower = mb_strtolower(trim($label));
                $hex = '#cccccc';
                foreach ($colorMap as $keyword => $color) {
                    if (str_contains($colorLower, $keyword)) {
                        $hex = $color;
                        break;
                    }
                }

                return [
                    'label' => $label,
                    'color' => $hex,
                    'value' => (float) $item->price,
                ];
            })->values()->toArray();

            return [
                'name' => $key,
                'value' => 0,
                'variants' => $variants,
            ];
        })->values()->toArray();

        return view('tools.stone-calculator', [
            'quickValuesFromMarketing' => $quickValues,
        ]);
    }
}
