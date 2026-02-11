<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Valuation\DTOs\ValuationChecklistData;
use App\Domain\Valuation\Enums\ListingSource;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use App\Domain\Valuation\Services\PriceCalculatorService;
use App\Domain\Valuation\Services\ValuationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ValuationController extends Controller
{
    public function __construct(
        private readonly ValuationService $valuationService,
        private readonly PriceCalculatorService $priceCalculator,
    ) {}

    /**
     * Página principal do avaliador de seminovos.
     */
    public function index(): View
    {
        $models = IphoneModel::active()
            ->orderBy('name')
            ->get()
            ->map(fn (IphoneModel $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'storages' => $m->storages,
                'colors' => $m->colors,
            ]);

        return view('valuations.index', compact('models'));
    }

    /**
     * API: retorna dados de preço para um modelo + storage.
     */
    public function getPrice(Request $request): JsonResponse
    {
        $request->validate([
            'model_id' => 'required|string',
            'storage' => 'required|string',
        ]);

        $data = $this->valuationService->getPriceData(
            $request->input('model_id'),
            $request->input('storage'),
        );

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sem dados de preço disponíveis para este modelo/armazenamento.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * API: avalia um seminovo com base no checklist.
     */
    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'iphone_model_id' => 'required|string|exists:iphone_models,id',
            'storage' => 'required|string',
            'battery_percentage' => 'required|integer|min:0|max:100',
            'device_state' => 'required|string|in:original,repaired',
            'accessory_state' => 'required|string|in:complete,partial,none',
            'color' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $checklist = ValuationChecklistData::fromArray($validated);
        $evaluation = $this->valuationService->evaluate($checklist);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Sem dados de mercado disponíveis para este modelo.',
            ], 404);
        }

        // Serializa para JSON (remove objetos complexos)
        $response = [
            'model_name' => $evaluation['model']->name,
            'storage' => $evaluation['storage'],
            'color' => $evaluation['color'],
            'battery_percentage' => $evaluation['battery_percentage'],
            'battery_health_label' => $evaluation['battery_health']->label(),
            'device_state_label' => $evaluation['device_state']->label(),
            'accessory_state_label' => $evaluation['accessory_state']->label(),
            'notes' => $evaluation['notes'],
            'market_avg' => $evaluation['market_avg'],
            'market_min' => $evaluation['market_min'],
            'market_max' => $evaluation['market_max'],
            'market_median' => $evaluation['market_median'],
            'sample_count' => $evaluation['sample_count'],
            'data_age_days' => $evaluation['data_age_days'],
            'modifiers' => $evaluation['modifiers'],
            'base_discount' => $evaluation['base_discount'],
            'total_discount' => $evaluation['total_discount'],
            'suggested_buy_price' => $evaluation['suggested_buy_price'],
            'message' => $this->valuationService->formatMessage($evaluation),
        ];

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }

    /**
     * API: insere preço manual de mercado.
     */
    public function storeManualPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'iphone_model_id' => 'required|string|exists:iphone_models,id',
            'storage' => 'required|string',
            'price' => 'required|numeric|min:100',
            'title' => 'nullable|string|max:255',
        ]);

        $model = IphoneModel::findOrFail($validated['iphone_model_id']);

        MarketListing::create([
            'iphone_model_id' => $model->id,
            'storage' => $validated['storage'],
            'title' => $validated['title'] ?: "{$model->name} {$validated['storage']} (manual)",
            'price' => $validated['price'],
            'source' => ListingSource::Manual,
            'location' => 'São José do Rio Preto, SP',
            'scraped_at' => now()->toDateString(),
        ]);

        // Recalcula média para este modelo+storage
        $this->priceCalculator->calculateForModelStorage($model, $validated['storage']);

        return response()->json([
            'success' => true,
            'message' => 'Preço adicionado e médias recalculadas.',
        ]);
    }
}
