<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Marketing\Models\MarketingCreative;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingResaleItem;
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

        if ($creatives->isEmpty()) {
            $lastCreative = MarketingCreative::latest('date')->first();
            if ($lastCreative) {
                $creativeDate = $lastCreative->date->toDateString();
                $creatives = MarketingCreative::with('user')
                    ->byDate($creativeDate)
                    ->latest()
                    ->get();
            }
        }

        $usedProducts = Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->whereIn('condition', ['used', 'refurbished'])
            ->orderBy('name')
            ->get();

        $allConsignmentItems = ConsignmentStockItem::available()
            ->where('available_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $usedListings = MarketingUsedListing::all()
            ->keyBy(fn ($l) => $l->listable_type . '_' . $l->listable_id);

        $pricesJson = $prices->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'storage' => $p->storage,
                'color' => $p->color,
                'price' => $p->price,
                'notes' => $p->notes,
                'active' => $p->active,
            ];
        })->values();

        $usedProductsJson = $usedProducts->map(function ($p) {
            return [
                'id' => $p->id,
                'morph_type' => Product::class,
                'name' => $p->name,
                'model' => $p->model,
                'storage' => $p->storage,
                'color' => $p->color,
                'condition' => $p->condition->value,
                'stock' => $p->stock_quantity,
            ];
        })->values();

        $mapConsignment = fn ($c) => [
            'id' => $c->id,
            'morph_type' => ConsignmentStockItem::class,
            'name' => $c->name,
            'model' => $c->model,
            'storage' => $c->storage,
            'color' => $c->color,
            'condition' => $c->condition?->value ?? 'new',
            'stock' => $c->available_quantity,
            'supplier_cost' => (float) $c->supplier_cost,
            'suggested_price' => (float) $c->suggested_price,
        ];

        $consignmentUsedJson = $allConsignmentItems
            ->filter(fn ($c) => ($c->condition?->value ?? 'new') === 'used')
            ->map($mapConsignment)
            ->values();

        $resaleItems = MarketingResaleItem::all()
            ->keyBy(fn ($r) => $r->resaleable_type . '_' . $r->resaleable_id);

        $consignmentResaleJson = $allConsignmentItems->map(fn ($c) => [
            'id' => $c->id,
            'morph_type' => ConsignmentStockItem::class,
            'name' => $c->name,
            'storage' => $c->storage,
            'color' => $c->color,
            'condition' => $c->condition?->value ?? 'new',
            'suggested_price' => (float) $c->suggested_price,
            'available_quantity' => $c->available_quantity,
        ])->values();

        $usedResaleJson = $usedProducts->map(fn ($p) => [
            'id' => $p->id,
            'morph_type' => Product::class,
            'name' => $p->name,
            'storage' => $p->storage,
            'color' => $p->color,
            'condition' => $p->condition->value,
            'stock' => $p->stock_quantity,
        ])->values();

        return view('marketing.index', [
            'prices' => $prices,
            'pricesJson' => $pricesJson,
            'creatives' => $creatives,
            'creativeDate' => $creativeDate,
            'usedProductsJson' => $usedProductsJson,
            'consignmentUsedJson' => $consignmentUsedJson,
            'usedListings' => $usedListings,
            'consignmentResaleJson' => $consignmentResaleJson,
            'usedResaleJson' => $usedResaleJson,
            'resaleItems' => $resaleItems,
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

    public function showCreativeImage(MarketingCreative $creative)
    {
        if (!$creative->image_path || !Storage::disk('public')->exists($creative->image_path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($creative->image_path);
        $mime = Storage::disk('public')->mimeType($creative->image_path);

        return response($file, 200)->header('Content-Type', $mime);
    }

    public function downloadCreativeImage(MarketingCreative $creative)
    {
        if (!$creative->image_path || !Storage::disk('public')->exists($creative->image_path)) {
            return redirect()->back()->with('error', 'Imagem não encontrada.');
        }

        $extension = pathinfo($creative->image_path, PATHINFO_EXTENSION);
        $safeName = str($creative->title)->slug() . '.' . $extension;

        return Storage::disk('public')->download($creative->image_path, $safeName);
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
            'listable_type' => ['required', 'string'],
            'listable_id' => ['required', 'string'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'final_price' => ['nullable', 'numeric', 'min:0'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'has_box' => ['nullable'],
            'has_cable' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'visible' => ['nullable'],
        ]);

        $listing = MarketingUsedListing::updateOrCreate(
            [
                'listable_type' => $request->listable_type,
                'listable_id' => $request->listable_id,
            ],
            [
                'cost_price' => $request->cost_price,
                'final_price' => $request->final_price,
                'battery_health' => $request->battery_health,
                'has_box' => $request->boolean('has_box'),
                'has_cable' => $request->boolean('has_cable'),
                'notes' => $request->notes,
                'visible' => $request->boolean('visible'),
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

    public function storeResaleItem(Request $request): JsonResponse
    {
        $request->validate([
            'resaleable_type' => ['required', 'string'],
            'resaleable_id' => ['required', 'string'],
            'resale_price' => ['nullable', 'numeric', 'min:0'],
            'battery_health' => ['nullable', 'integer', 'min:0', 'max:100'],
            'warranty_until' => ['nullable', 'date'],
            'has_box' => ['nullable'],
            'has_cable' => ['nullable'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'visible' => ['nullable'],
        ]);

        $item = MarketingResaleItem::updateOrCreate(
            [
                'resaleable_type' => $request->resaleable_type,
                'resaleable_id' => $request->resaleable_id,
            ],
            [
                'resale_price' => $request->resale_price,
                'battery_health' => $request->battery_health,
                'warranty_until' => $request->warranty_until,
                'has_box' => $request->boolean('has_box'),
                'has_cable' => $request->boolean('has_cable'),
                'notes' => $request->notes,
                'visible' => $request->boolean('visible'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Item de repasse salvo!',
            'item' => $item,
        ]);
    }

    public function toggleResaleVisibility(MarketingResaleItem $item): JsonResponse
    {
        $item->update(['visible' => !$item->visible]);

        return response()->json([
            'success' => true,
            'visible' => $item->visible,
        ]);
    }
}
