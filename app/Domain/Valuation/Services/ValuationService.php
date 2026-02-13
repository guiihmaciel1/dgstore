<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\DTOs\ValuationChecklistData;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\PriceAverage;

class ValuationService
{
    /**
     * Desconto base para compra de seminovos (margem DG Store = 20%).
     */
    private const BASE_DISCOUNT = 0.20;

    /**
     * Avalia um iPhone seminovo com base no checklist e preços de mercado.
     *
     * Fluxo:
     * 1. Obtém a média de preço de USADO (pesquisa manual no marketplace)
     * 2. Aplica modificadores (bateria, estado, acessórios)
     * 3. Aplica margem DG Store (-20%)
     * 4. Resultado = valor sugerido de compra
     */
    public function evaluate(ValuationChecklistData $checklist): ?array
    {
        $model = IphoneModel::find($checklist->iphoneModelId);

        if (! $model) {
            return null;
        }

        $priceAverage = $model->latestPriceAverage($checklist->storage);

        if (! $priceAverage) {
            return null;
        }

        // Preço de mercado (usado — pesquisa no marketplace)
        $marketAvg = (float) $priceAverage->avg_price;
        $marketMin = (float) $priceAverage->min_price;
        $marketMax = (float) $priceAverage->max_price;

        // Modificadores do checklist (bateria, estado, acessórios)
        $modifier = $checklist->totalModifier();

        // Valor sugerido = preço usado * (1 - margem - ajustes)
        $totalDiscount = self::BASE_DISCOUNT - $modifier;
        $suggestedBuyPrice = round($marketAvg * (1 - $totalDiscount), 2);

        return [
            'model' => $model,
            'model_name' => $model->name,
            'storage' => $checklist->storage,
            'color' => $checklist->color,
            'battery_percentage' => $checklist->batteryPercentage,
            'battery_health' => $checklist->batteryHealth(),
            'battery_health_label' => $checklist->batteryHealth()->label(),
            'device_state' => $checklist->deviceState,
            'device_state_label' => $checklist->deviceState->label(),
            'accessory_state' => $checklist->accessoryState,
            'accessory_state_label' => $checklist->accessoryState->label(),
            'notes' => $checklist->notes,

            // Preços de mercado (usado)
            'market_avg' => $marketAvg,
            'market_min' => $marketMin,
            'market_max' => $marketMax,

            'sample_count' => $priceAverage->sample_count,
            'data_age_days' => $priceAverage->days_old,

            // Modificadores
            'modifiers' => [
                'battery' => $checklist->batteryHealth()->priceModifier(),
                'device_state' => $checklist->deviceState->priceModifier(),
                'accessories' => $checklist->accessoryState->priceModifier(),
                'total' => $modifier,
            ],
            'base_discount' => self::BASE_DISCOUNT,
            'total_discount' => $totalDiscount,
            'suggested_buy_price' => $suggestedBuyPrice,
        ];
    }

    /**
     * Gera mensagem formatada da avaliação para copiar/WhatsApp.
     */
    public function formatMessage(array $evaluation): string
    {
        $model = $evaluation['model'];
        $priceFormat = fn (float $v) => 'R$ ' . number_format($v, 2, ',', '.');

        $lines = [
            'Avaliação de Seminovo — DG Store',
            '',
            "Modelo: {$model->name}",
            "Capacidade: {$evaluation['storage']}",
        ];

        if ($evaluation['color']) {
            $lines[] = "Cor: {$evaluation['color']}";
        }

        $lines[] = "Bateria: {$evaluation['battery_percentage']}% ({$evaluation['battery_health']->label()})";
        $lines[] = "Estado: {$evaluation['device_state']->label()}";
        $lines[] = "Acessórios: {$evaluation['accessory_state']->label()}";

        if ($evaluation['notes']) {
            $lines[] = "Obs: {$evaluation['notes']}";
        }

        $lines[] = '';
        $lines[] = "Preço de mercado (usado): {$priceFormat($evaluation['market_avg'])}";
        $lines[] = "  Faixa: {$priceFormat($evaluation['market_min'])} — {$priceFormat($evaluation['market_max'])}";
        $lines[] = "  Baseado em {$evaluation['sample_count']} anúncios";
        $lines[] = '';
        $lines[] = 'Valor sugerido de compra:';
        $lines[] = "  {$priceFormat($evaluation['suggested_buy_price'])}";
        $lines[] = '  (Margem DG Store: -' . round(self::BASE_DISCOUNT * 100) . '%)';

        return implode("\n", $lines);
    }

    /**
     * Retorna dados de preço para a API (usado pelo Alpine.js).
     */
    public function getPriceData(string $modelId, string $storage): ?array
    {
        $model = IphoneModel::find($modelId);

        if (! $model) {
            return null;
        }

        $priceAverage = $model->latestPriceAverage($storage);

        if (! $priceAverage) {
            return null;
        }

        return [
            'avg_price' => (float) $priceAverage->avg_price,
            'median_price' => (float) $priceAverage->median_price,
            'min_price' => (float) $priceAverage->min_price,
            'max_price' => (float) $priceAverage->max_price,
            'suggested_buy_price' => (float) $priceAverage->suggested_buy_price,
            'sample_count' => $priceAverage->sample_count,
            'calculated_at' => $priceAverage->calculated_at->format('d/m/Y'),
            'days_old' => $priceAverage->days_old,
        ];
    }
}
