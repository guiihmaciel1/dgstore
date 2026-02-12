<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\MarketListing;
use App\Domain\Valuation\Models\PriceAverage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PriceCalculatorService
{
    /**
     * Desconto base para sugestão de preço de compra (30%).
     */
    private const BUY_DISCOUNT = 0.30;

    /**
     * Número de dias de dados a considerar.
     */
    private const DAYS_WINDOW = 7;

    /**
     * Mínimo de amostras para gerar uma média válida.
     */
    private const MIN_SAMPLES = 3;

    /**
     * Calcula médias para todos os modelos e variantes de storage.
     *
     * @return array Resumo: total de médias calculadas e erros
     */
    public function calculateAll(): array
    {
        $models = IphoneModel::active()->get();
        $totalCalculated = 0;
        $errors = [];

        foreach ($models as $model) {
            foreach ($model->storages as $storage) {
                try {
                    $calculated = $this->calculateForModelStorage($model, $storage);
                    if ($calculated) {
                        $totalCalculated++;
                    }
                } catch (\Throwable $e) {
                    $errors[] = "{$model->name} {$storage}: {$e->getMessage()}";
                    Log::error("[Price Calculator] Erro: {$model->name} {$storage} - {$e->getMessage()}");
                }
            }
        }

        return [
            'total_calculated' => $totalCalculated,
            'errors' => $errors,
        ];
    }

    /**
     * Calcula a média de preço para um modelo+storage específico.
     * Usa todos os preços disponíveis (novos do catálogo ML + manuais).
     */
    public function calculateForModelStorage(IphoneModel $model, string $storage): bool
    {
        $prices = MarketListing::forModel($model->id, $storage)
            ->recent(self::DAYS_WINDOW)
            ->pluck('price')
            ->map(fn ($p) => (float) $p)
            ->sort()
            ->values();

        if ($prices->count() < self::MIN_SAMPLES) {
            Log::info("[Price Calculator] {$model->name} {$storage}: apenas {$prices->count()} amostras (mínimo: " . self::MIN_SAMPLES . ')');

            return false;
        }

        Log::info("[Price Calculator] {$model->name} {$storage}: {$prices->count()} amostras");

        // Remove outliers via IQR
        $filtered = $this->removeOutliers($prices);

        if ($filtered->isEmpty()) {
            Log::warning("[Price Calculator] {$model->name} {$storage}: todos os preços são outliers.");
            return false;
        }

        $avgPrice = round($filtered->avg(), 2);
        $medianPrice = round($this->calculateMedian($filtered), 2);
        $suggestedBuyPrice = round($avgPrice * (1 - self::BUY_DISCOUNT), 2);

        PriceAverage::updateOrCreate(
            [
                'iphone_model_id' => $model->id,
                'storage' => $storage,
                'calculated_at' => now()->toDateString(),
            ],
            [
                'avg_price' => $avgPrice,
                'median_price' => $medianPrice,
                'min_price' => $filtered->min(),
                'max_price' => $filtered->max(),
                'suggested_buy_price' => $suggestedBuyPrice,
                'sample_count' => $filtered->count(),
            ],
        );

        Log::info("[Price Calculator] {$model->name} {$storage}: média R$ {$avgPrice} ({$filtered->count()} amostras)");

        return true;
    }

    /**
     * Remove outliers usando o método IQR (Interquartile Range).
     *
     * Valores fora do intervalo [Q1 - 1.5*IQR, Q3 + 1.5*IQR] são removidos.
     */
    private function removeOutliers(Collection $prices): Collection
    {
        $sorted = $prices->sort()->values();
        $count = $sorted->count();

        if ($count < 4) {
            return $sorted;
        }

        $q1 = $this->percentile($sorted, 25);
        $q3 = $this->percentile($sorted, 75);
        $iqr = $q3 - $q1;

        $lowerBound = $q1 - (1.5 * $iqr);
        $upperBound = $q3 + (1.5 * $iqr);

        return $sorted->filter(fn (float $price) => $price >= $lowerBound && $price <= $upperBound)->values();
    }

    /**
     * Calcula um percentil de uma coleção ordenada.
     */
    private function percentile(Collection $sorted, float $percentile): float
    {
        $count = $sorted->count();
        $index = ($percentile / 100) * ($count - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);
        $fraction = $index - $lower;

        if ($lower === $upper) {
            return $sorted[$lower];
        }

        return $sorted[$lower] + $fraction * ($sorted[$upper] - $sorted[$lower]);
    }

    /**
     * Calcula a mediana de uma coleção.
     */
    private function calculateMedian(Collection $sorted): float
    {
        return $this->percentile($sorted->sort()->values(), 50);
    }
}
