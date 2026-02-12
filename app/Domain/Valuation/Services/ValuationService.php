<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\DTOs\ValuationChecklistData;
use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\PriceAverage;

class ValuationService
{
    /**
     * Desconto base para compra de seminovos (margem DG Store = 30%).
     */
    private const BASE_DISCOUNT = 0.30;

    /**
     * Avalia um iPhone seminovo com base no checklist e preços de mercado.
     *
     * Fluxo:
     * 1. Obtém a média de preço de NOVO (catálogo ML)
     * 2. Aplica fator de depreciação por geração → estimativa de USADO
     * 3. Aplica modificadores (bateria, estado, acessórios)
     * 4. Aplica margem DG Store (-30%)
     * 5. Resultado = valor sugerido de compra
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

        // Preço de mercado (novo)
        $marketNewAvg = (float) $priceAverage->avg_price;
        $marketNewMin = (float) $priceAverage->min_price;
        $marketNewMax = (float) $priceAverage->max_price;

        // Estimativa de usado (novo * fator de depreciação)
        $depreciationFactor = $model->depreciationFactor();
        $usedEstimate = round($marketNewAvg * $depreciationFactor, 2);

        // Modificadores do checklist (bateria, estado, acessórios)
        $modifier = $checklist->totalModifier();

        // Valor sugerido = usado estimado * (1 - margem - ajustes_negativos)
        $totalDiscount = self::BASE_DISCOUNT - $modifier;
        $suggestedBuyPrice = round($usedEstimate * (1 - $totalDiscount), 2);

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

            // Preços de mercado (novo — catálogo ML)
            'market_new_avg' => $marketNewAvg,
            'market_new_min' => $marketNewMin,
            'market_new_max' => $marketNewMax,

            // Estimativa de usado
            'depreciation_factor' => $depreciationFactor,
            'depreciation_pct' => round((1 - $depreciationFactor) * 100),
            'used_estimate' => $usedEstimate,

            // Dados para retrocompatibilidade com a view
            'market_avg' => $usedEstimate,
            'market_min' => round($marketNewMin * $depreciationFactor, 2),
            'market_max' => round($marketNewMax * $depreciationFactor, 2),

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
        $lines[] = "Preço de mercado (novo): {$priceFormat($evaluation['market_new_avg'])}";
        $lines[] = "Estimativa usado ({$evaluation['depreciation_pct']}% dep.): {$priceFormat($evaluation['used_estimate'])}";
        $lines[] = "  Baseado em {$evaluation['sample_count']} anúncios do ML";
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

        $depreciationFactor = $model->depreciationFactor();

        return [
            'avg_price' => (float) $priceAverage->avg_price,
            'median_price' => (float) $priceAverage->median_price,
            'min_price' => (float) $priceAverage->min_price,
            'max_price' => (float) $priceAverage->max_price,
            'depreciation_factor' => $depreciationFactor,
            'used_estimate' => round((float) $priceAverage->avg_price * $depreciationFactor, 2),
            'suggested_buy_price' => (float) $priceAverage->suggested_buy_price,
            'sample_count' => $priceAverage->sample_count,
            'calculated_at' => $priceAverage->calculated_at->format('d/m/Y'),
            'days_old' => $priceAverage->days_old,
        ];
    }
}
