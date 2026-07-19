<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\AI\Services\GeminiService;
use App\Domain\ConsignmentStock\Models\ConsignmentStockItem;
use App\Domain\Marketing\Models\MarketingContent;
use App\Domain\Marketing\Models\MarketingCreative;
use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Marketing\Models\MarketingPriceImage;
use App\Domain\Marketing\Models\MarketingResaleItem;
use App\Domain\Marketing\Models\MarketingUsedListing;
use App\Domain\Marketing\Models\MarketingUsedListingImage;
use App\Domain\Product\Models\Product;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Controllers\Concerns\CompressesImages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MarketingController extends Controller
{
    use CompressesImages;
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

        $isAdmin = auth()->user()->role->isAdminGeral();

        $usedListings = MarketingUsedListing::with('images')->get()
            ->keyBy(fn ($l) => $l->listable_type . '_' . $l->listable_id);

        if (! $isAdmin) {
            $usedListings = $usedListings->map(function ($listing) {
                $listing->makeHidden('cost_price');
                return $listing;
            });
        }

        $pricesJson = $prices->map(function ($p) use ($isAdmin) {
            $data = [
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

            if ($isAdmin) {
                $data['cost_price'] = $p->cost_price;
            }

            return $data;
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
                'product_notes' => $p->notes,
            ];
        })->values();

        $mapConsignment = function ($c) use ($isAdmin) {
            $data = [
                'id' => $c->id,
                'morph_type' => ConsignmentStockItem::class,
                'name' => $c->name,
                'model' => $c->model,
                'storage' => $c->storage,
                'color' => $c->color,
                'condition' => $c->condition?->value ?? 'new',
                'stock' => $c->available_quantity,
                'suggested_price' => (float) $c->suggested_price,
                'battery_health' => $c->battery_health,
                'has_box' => (bool) $c->has_box,
                'has_cable' => (bool) $c->has_cable,
            ];

            if ($isAdmin) {
                $data['supplier_cost'] = (float) $c->supplier_cost;
            }

            return $data;
        };

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

        $contentsJson = MarketingContent::with('user')
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'description' => $c->description,
                'type' => $c->type,
                'type_label' => $c->getTypeLabel(),
                'platform' => $c->platform,
                'platform_label' => $c->getPlatformLabel(),
                'status' => $c->status,
                'status_label' => $c->getStatusLabel(),
                'scheduled_at' => $c->scheduled_at?->format('Y-m-d'),
                'scheduled_at_formatted' => $c->scheduled_at?->format('d/m/Y'),
                'image_url' => $c->image_url,
                'ai_generated' => $c->ai_generated,
                'user_name' => $c->user?->name ?? 'Sistema',
                'created_at' => $c->created_at->format('d/m/Y H:i'),
            ])
            ->values();

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
            'contentsJson' => $contentsJson,
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
            'prices.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'prices.*.notes' => ['nullable', 'string', 'max:500'],
            'prices.*.active' => ['nullable'],
        ]);

        $isAdmin = auth()->user()->role->isAdminGeral();

        DB::transaction(function () use ($request, $isAdmin) {
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

                if ($isAdmin) {
                    $data['cost_price'] = !empty($row['cost_price']) ? $row['cost_price'] : null;
                }

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

        $listingData = [
            'final_price' => $request->final_price,
            'battery_health' => $request->battery_health,
            'has_box' => $request->boolean('has_box'),
            'has_cable' => $request->boolean('has_cable'),
            'notes' => $request->notes,
            'visible' => $request->boolean('visible'),
        ];

        $productData = [
            'sale_price' => $request->final_price,
            'battery_health' => $request->battery_health,
            'has_box' => $request->boolean('has_box'),
            'has_cable' => $request->boolean('has_cable'),
            'notes' => $request->notes,
        ];

        if (auth()->user()->role->isAdminGeral()) {
            $listingData['cost_price'] = $request->cost_price;
            $productData['cost_price'] = $request->cost_price;
        }

        $listing = MarketingUsedListing::updateOrCreate(
            [
                'listable_type' => $request->listable_type,
                'listable_id' => $request->listable_id,
            ],
            $listingData
        );

        if ($request->listable_type === Product::class) {
            $product = Product::find($request->listable_id);
            if ($product) {
                $product->update(array_filter($productData, fn ($v) => $v !== null));
            }
        }

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

        if ($request->resaleable_type === Product::class) {
            $product = Product::find($request->resaleable_id);
            if ($product) {
                $product->update(['resale_price' => $request->resale_price]);
            }
        }

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

    // ─── Conteúdos ───────────────────────────────────────────

    public function storeContent(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'string', 'in:reels,stories,post,carousel'],
            'platform' => ['required', 'string', 'in:instagram,tiktok,whatsapp,all'],
            'status' => ['required', 'string', 'in:idea,production,published'],
            'scheduled_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:5120'],
            'ai_generated' => ['nullable'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('marketing-contents', 'public');
        }

        $content = MarketingContent::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'platform' => $request->platform,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
            'image_path' => $imagePath,
            'ai_generated' => $request->boolean('ai_generated'),
        ]);

        $content->load('user');

        return response()->json([
            'success' => true,
            'content' => [
                'id' => $content->id,
                'title' => $content->title,
                'description' => $content->description,
                'type' => $content->type,
                'type_label' => $content->getTypeLabel(),
                'platform' => $content->platform,
                'platform_label' => $content->getPlatformLabel(),
                'status' => $content->status,
                'status_label' => $content->getStatusLabel(),
                'scheduled_at' => $content->scheduled_at?->format('Y-m-d'),
                'scheduled_at_formatted' => $content->scheduled_at?->format('d/m/Y'),
                'image_url' => $content->image_url,
                'ai_generated' => $content->ai_generated,
                'user_name' => $content->user?->name ?? 'Sistema',
                'created_at' => $content->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function updateContent(Request $request, MarketingContent $content): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'string', 'in:reels,stories,post,carousel'],
            'platform' => ['required', 'string', 'in:instagram,tiktok,whatsapp,all'],
            'status' => ['required', 'string', 'in:idea,production,published'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $content->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'platform' => $request->platform,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        $content->load('user');

        return response()->json([
            'success' => true,
            'content' => [
                'id' => $content->id,
                'title' => $content->title,
                'description' => $content->description,
                'type' => $content->type,
                'type_label' => $content->getTypeLabel(),
                'platform' => $content->platform,
                'platform_label' => $content->getPlatformLabel(),
                'status' => $content->status,
                'status_label' => $content->getStatusLabel(),
                'scheduled_at' => $content->scheduled_at?->format('Y-m-d'),
                'scheduled_at_formatted' => $content->scheduled_at?->format('d/m/Y'),
                'image_url' => $content->image_url,
                'ai_generated' => $content->ai_generated,
                'user_name' => $content->user?->name ?? 'Sistema',
                'created_at' => $content->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function deleteContent(MarketingContent $content): JsonResponse
    {
        if ($content->image_path) {
            Storage::disk('public')->delete($content->image_path);
        }

        $content->delete();

        return response()->json(['success' => true]);
    }

    public function generateContentIdeas(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => ['nullable', 'string', 'max:500'],
        ]);

        $gemini = app(GeminiService::class);

        if (! $gemini->isAvailable()) {
            return response()->json(['error' => 'Serviço de IA não disponível.'], 503);
        }

        $topicContext = $request->topic
            ? "O usuário pediu ideias sobre: \"{$request->topic}\"\n\n"
            : '';

        $prompt = <<<PROMPT
{$topicContext}Gere exatamente 5 ideias criativas de conteúdo para redes sociais de uma loja Apple Premium (iPhones, MacBooks, iPads, Apple Watch, AirPods).

Cada ideia deve ser sobre UM destes temas (varie entre eles):
- Tendências atuais do universo Apple
- Curiosidades e fatos interessantes sobre iPhone
- Novidades e lançamentos recentes da Apple
- Dicas práticas de uso do iPhone/Apple
- Comparativos entre modelos Apple

FORMATO DE SAÍDA (array JSON, sem markdown):
[{
  "title": "Título chamativo e curto (máx 60 chars)",
  "description": "Roteiro/descrição do conteúdo em 2-3 frases. Inclua o que mostrar, o que falar, e um gancho de engajamento.",
  "type": "reels|stories|post|carousel",
  "platform": "instagram|tiktok|whatsapp|all"
}]

REGRAS:
1. Títulos devem ser chamativos e gerar curiosidade
2. Descrições devem ser práticas e acionáveis para o social media
3. Varie os tipos (reels, stories, post, carousel)
4. Varie as plataformas
5. Conteúdo em português brasileiro, tom jovem e profissional
6. Foque em conteúdos que uma LOJA DE CELULARES publicaria
7. NÃO invente especificações técnicas incorretas
PROMPT;

        $systemInstruction = 'Você é um social media especializado em marketing para lojas Apple/iPhone no Brasil. '
            . 'Conhece as últimas tendências, lançamentos e curiosidades do ecossistema Apple. '
            . 'Gere ideias criativas e engajantes. Retorne APENAS o array JSON, sem explicações.';

        $ideas = $gemini->generateJson($prompt, $systemInstruction);

        if ($ideas === null) {
            return response()->json(['error' => 'Não foi possível gerar ideias. Tente novamente.'], 500);
        }

        $validated = collect($ideas)
            ->filter(fn ($item) => is_array($item) && ! empty($item['title']))
            ->map(fn ($item) => [
                'title' => mb_substr(trim($item['title'] ?? ''), 0, 255),
                'description' => trim($item['description'] ?? ''),
                'type' => in_array($item['type'] ?? '', ['reels', 'stories', 'post', 'carousel'])
                    ? $item['type'] : 'post',
                'platform' => in_array($item['platform'] ?? '', ['instagram', 'tiktok', 'whatsapp', 'all'])
                    ? $item['platform'] : 'instagram',
            ])
            ->values()
            ->all();

        return response()->json(['ideas' => $validated]);
    }
}
