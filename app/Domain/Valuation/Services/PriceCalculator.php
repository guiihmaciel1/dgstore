<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

class PriceCalculator
{
    public function trimOutliers(array $sortedPrices, int $percentage): array
    {
        $count = count($sortedPrices);
        if ($count <= 4) {
            return $sortedPrices;
        }

        $trimCount = (int) floor($count * ($percentage / 100));

        return array_slice($sortedPrices, $trimCount, $count - ($trimCount * 2));
    }

    public function calculateStats(array $prices): array
    {
        if (empty($prices)) {
            return ['average' => 0, 'median' => 0, 'min' => 0, 'max' => 0, 'std_dev' => 0];
        }

        $count = count($prices);
        $average = array_sum($prices) / $count;
        $min = min($prices);
        $max = max($prices);

        sort($prices);
        $middle = (int) floor($count / 2);
        $median = ($count % 2 === 0)
            ? ($prices[$middle - 1] + $prices[$middle]) / 2
            : $prices[$middle];

        $variance = array_sum(array_map(fn ($p) => ($p - $average) ** 2, $prices)) / $count;
        $std_dev = sqrt($variance);

        return compact('average', 'median', 'min', 'max', 'std_dev');
    }

    /**
     * Preco = floor(Mercado * (1 - (margem - modificadores)) / 100) * 100
     */
    public function calculateSuggestedPrice(
        float $marketAvg,
        float $margin,
        float $batteryMod,
        float $deviceStateMod,
        float $accessoryMod,
    ): float {
        $totalModifier = $batteryMod + $deviceStateMod + $accessoryMod;
        $totalDiscount = ($margin / 100) - ($totalModifier / 100);
        $raw = $marketAvg * (1 - $totalDiscount);

        return floor($raw / 100) * 100;
    }

    public function calculateResalePrice(float $suggestedBuyPrice, float $resaleMargin): float
    {
        return floor($suggestedBuyPrice * (1 + $resaleMargin / 100) / 100) * 100;
    }
}
