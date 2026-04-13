<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Marketing\Models\MarketingCreative;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingPriceImage;
use App\Domain\Marketing\Models\MarketingResaleItem;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Marketing\Models\MarketingUsedListingImage;
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
        $prices = MarketingPrice::with('images')->ordered()->get();

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

        $newProducts = Product::where('active', true)
            ->where('stock_quantity', '>', 0)
            ->where('condition', 'new')
            ->orderBy('name')
            ->get();

        $allConsignmentItems = ConsignmentStockItem::available()
            ->where('available_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $usedListings = MarketingUsedListing::with('images')->get()
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
                'images' => $p->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'original_name' => $img->original_name,
                ])->values(),
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
            'battery_health' => $c->battery_health,
            'has_box' => (bool) $c->has_box,
            'has_cable' => (bool) $c->has_cable,
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
            'battery_health' => $c->battery_health,
            'has_box' => (bool) $c->has_box,
            'has_cable' => (bool) $c->has_cable,
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

        $newProductsResaleJson = $newProducts->map(fn ($p) => [
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
            'newProductsResaleJson' => $newProductsResaleJson,
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
                $orphanImages = MarketingPriceImage::whereIn('marketing_price_id', $toDelete)->get();
                foreach ($orphanImages as $img) {
                    Storage::disk('public')->delete($img->path);
                }
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
        foreach ($listing->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dados do seminovo removidos!',
        ]);
    }

    public function storeUsedListingImage(Request $request): JsonResponse
    {
        $request->validate([
            'marketing_used_listing_id' => ['required', 'string', 'exists:marketing_used_listings,id'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $listingId = $request->input('marketing_used_listing_id');
        $existing = MarketingUsedListingImage::where('marketing_used_listing_id', $listingId)->count();

        if ($existing >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Limite de 5 imagens por seminovo atingido.',
            ], 422);
        }

        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();

        $directory = "marketing-used-listings/{$listingId}";
        Storage::disk('public')->makeDirectory($directory);

        $filename = uniqid() . '.jpg';
        $relativePath = "{$directory}/{$filename}";
        $fullPath = Storage::disk('public')->path($relativePath);

        $this->compressAndSaveImage($file->getRealPath(), $fullPath);

        $image = MarketingUsedListingImage::create([
            'marketing_used_listing_id' => $listingId,
            'path' => $relativePath,
            'original_name' => $originalName,
            'sort_order' => $existing,
        ]);

        return response()->json([
            'success' => true,
            'image' => [
                'id' => $image->id,
                'url' => $image->url,
                'original_name' => $image->original_name,
            ],
        ]);
    }

    public function deleteUsedListingImage(MarketingUsedListingImage $image): JsonResponse
    {
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }

    public function showPriceImage(MarketingPriceImage $image)
    {
        if (! $image->path || ! Storage::disk('public')->exists($image->path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($image->path);
        $mime = Storage::disk('public')->mimeType($image->path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function showUsedListingImage(MarketingUsedListingImage $image)
    {
        if (! $image->path || ! Storage::disk('public')->exists($image->path)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($image->path);
        $mime = Storage::disk('public')->mimeType($image->path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=86400');
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

    public function storePriceImage(Request $request): JsonResponse
    {
        $request->validate([
            'marketing_price_id' => ['required', 'string', 'exists:marketing_prices,id'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $priceId = $request->input('marketing_price_id');
        $existing = MarketingPriceImage::where('marketing_price_id', $priceId)->count();

        if ($existing >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Limite de 5 imagens por produto atingido.',
            ], 422);
        }

        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();

        $directory = "marketing-prices/{$priceId}";
        Storage::disk('public')->makeDirectory($directory);

        $filename = uniqid() . '.jpg';
        $relativePath = "{$directory}/{$filename}";
        $fullPath = Storage::disk('public')->path($relativePath);

        $this->compressAndSaveImage($file->getRealPath(), $fullPath);

        $image = MarketingPriceImage::create([
            'marketing_price_id' => $priceId,
            'path' => $relativePath,
            'original_name' => $originalName,
            'sort_order' => $existing,
        ]);

        return response()->json([
            'success' => true,
            'image' => [
                'id' => $image->id,
                'url' => $image->url,
                'original_name' => $image->original_name,
            ],
        ]);
    }

    public function deletePriceImage(MarketingPriceImage $image): JsonResponse
    {
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }

    public function reorderPriceImages(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ]);

        foreach ($request->input('ids') as $index => $id) {
            MarketingPriceImage::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    private function compressAndSaveImage(string $sourcePath, string $destinationPath): void
    {
        $dir = dirname($destinationPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $imageData = file_get_contents($sourcePath);
        $gdImage = @imagecreatefromstring($imageData);

        if ($gdImage === false) {
            copy($sourcePath, $destinationPath);

            return;
        }

        $width = imagesx($gdImage);
        $height = imagesy($gdImage);

        if ($width > 1200) {
            $newWidth = 1200;
            $newHeight = (int) ($height * 1200 / $width);
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $gdImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($gdImage);
            $gdImage = $resized;
        }

        imagejpeg($gdImage, $destinationPath, 85);
        imagedestroy($gdImage);
    }
}
