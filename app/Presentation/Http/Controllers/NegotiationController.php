<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Marketing\Models\MarketingPrice;
use App\Domain\Valuation\Services\NegotiationEvaluatorService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NegotiationController extends Controller
{
    public function __construct(
        private readonly NegotiationEvaluatorService $evaluatorService,
    ) {}

    public function index(): View
    {
        $quickValues = $this->buildQuickValues();
        $tradeInModels = config('dgifipe.models');

        return view('tools.negotiation-simulator', [
            'quickValuesFromMarketing' => $quickValues,
            'tradeInModels' => $tradeInModels,
        ]);
    }

    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => ['required', 'string'],
            'storage' => ['required', 'string'],
            'battery_health' => ['required', 'integer', 'min:0', 'max:100'],
            'device_state' => ['required', 'string', 'in:original,repaired'],
            'no_box' => ['boolean'],
            'no_cable' => ['boolean'],
        ]);

        $accessoryChecks = [
            'no_box' => ! empty($validated['no_box']),
            'no_cable' => ! empty($validated['no_cable']),
        ];

        try {
            $result = $this->evaluatorService->evaluate(
                model: $validated['model'],
                storage: $validated['storage'],
                batteryHealth: (int) $validated['battery_health'],
                deviceState: $validated['device_state'],
                accessoryChecks: $accessoryChecks,
            );

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao avaliar seminovo. Verifique a conexão com o banco de dados.',
            ], 500);
        }
    }

    private function buildQuickValues(): array
    {
        $prices = MarketingPrice::active()->ordered()->get();

        $grouped = $prices->groupBy(function ($p) {
            return trim($p->name . ' ' . ($p->storage ?? ''));
        });

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

        return $grouped->map(function ($items, $key) use ($colorMap) {
            if ($items->count() === 1 && (! $items->first()->color || $items->first()->color === 'Todas')) {
                return [
                    'name' => $key,
                    'value' => (float) $items->first()->price,
                ];
            }

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

                return ['label' => $label, 'color' => $hex, 'value' => (float) $item->price];
            })->values()->toArray();

            return ['name' => $key, 'value' => 0, 'variants' => $variants];
        })->values()->toArray();
    }
}
