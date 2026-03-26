<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Marketing\Models\MarketingCreative;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Product\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(Request $request): View
    {
        $prices = MarketingPrice::ordered()->get();

        $creativeDate = $request->get('date', today()->toDateString());
        $creatives = MarketingCreative::with('user')
            ->byDate($creativeDate)
            ->latest()
            ->get();

        $usedProducts = Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->whereIn('condition', ['used', 'refurbished'])
            ->orderBy('name')
            ->get();

        $usedListings = MarketingUsedListing::whereIn('product_id', $usedProducts->pluck('id'))
            ->get()
            ->keyBy('product_id');

        return view('marketing.index', [
            'prices' => $prices,
            'creatives' => $creatives,
            'creativeDate' => $creativeDate,
            'usedProducts' => $usedProducts,
            'usedListings' => $usedListings,
        ]);
    }

    public function storePrices(Request $request): RedirectResponse
    {
        $request->validate([
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.name' => ['required', 'string', 'max:255'],
            'prices.*.storage' => ['nullable', 'string', 'max:50'],
            'prices.*.color' => ['nullable', 'string', 'max:50'],
            'prices.*.price' => ['required', 'numeric', 'min:0'],
            'prices.*.notes' => ['nullable', 'string', 'max:500'],
            'prices.*.active' => ['nullable'],
        ]);

        DB::transaction(function () use ($request) {
            $existingIds = MarketingPrice::pluck('id')->toArray();
            $sentIds = [];

            foreach ($request->prices as $index => $row) {
                $data = [
                    'name' => $row['name'],
                    'storage' => $row['storage'] ?? null,
                    'color' => $row['color'] ?? null,
                    'price' => $row['price'],
                    'notes' => $row['notes'] ?? null,
                    'active' => isset($row['active']),
                    'sort_order' => $index,
                ];

                if (!empty($row['id'])) {
                    $price = MarketingPrice::find($row['id']);
                    if ($price) {
                        $price->update($data);
                        $sentIds[] = $price->id;
                        continue;
                    }
                }

                $created = MarketingPrice::create($data);
                $sentIds[] = $created->id;
            }

            $toDelete = array_diff($existingIds, $sentIds);
            if ($toDelete) {
                MarketingPrice::whereIn('id', $toDelete)->delete();
            }
        });

        return redirect()
            ->route('marketing.index', ['tab' => 'prices'])
            ->with('success', 'Tabela de preços atualizada com sucesso!');
    }

    public function storeCreative(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'date' => ['required', 'date'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('marketing-creatives', 'public');
        }

        MarketingCreative::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $imagePath,
            'date' => $request->date,
        ]);

        return redirect()
            ->route('marketing.index', ['tab' => 'creatives', 'date' => $request->date])
            ->with('success', 'Criativo adicionado com sucesso!');
    }

    public function deleteCreative(MarketingCreative $creative): RedirectResponse
    {
        if ($creative->image_path) {
            Storage::disk('public')->delete($creative->image_path);
        }

        $date = $creative->date->toDateString();
        $creative->delete();

        return redirect()
            ->route('marketing.index', ['tab' => 'creatives', 'date' => $date])
            ->with('success', 'Criativo removido com sucesso!');
    }

    public function storeUsedListing(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'trade_in_price' => ['nullable', 'numeric', 'min:0'],
            'resale_price' => ['nullable', 'numeric', 'min:0'],
            'final_price' => ['nullable', 'numeric', 'min:0'],
            'has_box' => ['nullable'],
            'has_cable' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $listing = MarketingUsedListing::updateOrCreate(
            ['product_id' => $request->product_id],
            [
                'cost_price' => $request->cost_price,
                'trade_in_price' => $request->trade_in_price,
                'resale_price' => $request->resale_price,
                'final_price' => $request->final_price,
                'has_box' => $request->boolean('has_box'),
                'has_cable' => $request->boolean('has_cable'),
                'notes' => $request->notes,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dados do seminovo salvos com sucesso!',
            'listing' => $listing,
        ]);
    }

    public function deleteUsedListing(MarketingUsedListing $listing): JsonResponse
    {
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dados do seminovo removidos!',
        ]);
    }
}
