<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NegotiationEvaluatorService
{
    public function __construct(
        private readonly PriceCalculator $calculator,
    ) {}

    public function evaluate(
        string $model,
        string $storage,
        int $batteryHealth,
        string $deviceState,
        array $accessoryChecks,
    ): array {
        $cities = config('dgifipe.cities');
        $lookbackDays = config('dgifipe.listing_lookback_days');

        $cacheKey = "negotiation:listings:{$model}:{$storage}";
        $cacheTtl = $this->secondsUntilNextScrape();

        $listingData = Cache::remember(
            $cacheKey,
            $cacheTtl,
            fn () => $this->fetchListingData($model, $storage, $cities, $lookbackDays),
        );

        $listings = $listingData['prices'];
        $lastCollectedAt = $listingData['last_collected_at'];
        $listingsCount = count($listings);
        $lowDataWarning = $listingsCount < config('dgifipe.min_listings_warning');

        if ($listingsCount === 0) {
            return [
                'market_average' => null,
                'median' => null,
                'price_min' => null,
                'price_max' => null,
                'suggested_price' => null,
                'resale_price' => null,
                'listings_count' => 0,
                'low_data_warning' => true,
                'confidence' => 'low',
                'std_dev' => 0,
                'last_collected_at' => null,
            ];
        }

        $trimmed = $this->calculator->trimOutliers($listings, config('dgifipe.trim_percentage'));
        $stats = $this->calculator->calculateStats($trimmed);

        $margin = (float) config('dgifipe.default_margin');
        $resaleMargin = (float) config('dgifipe.default_resale_margin');
        $batteryMod = $this->getBatteryModifier($batteryHealth);
        $deviceStateMod = $this->getDeviceStateModifier($deviceState);
        $accessoryLevel = self::resolveAccessoryLevel($accessoryChecks);
        $accessoryMod = $this->getAccessoryModifier($accessoryLevel);

        $suggestedPrice = $this->calculator->calculateSuggestedPrice(
            $stats['average'],
            $margin,
            $batteryMod,
            $deviceStateMod,
            $accessoryMod,
        );

        $resalePrice = $this->calculator->calculateResalePrice($suggestedPrice, $resaleMargin);
        $confidence = $this->calculateConfidence($listingsCount, $stats['std_dev'], $stats['average']);

        return [
            'market_average' => round($stats['average'], 2),
            'median' => round($stats['median'], 2),
            'price_min' => round($stats['min'], 2),
            'price_max' => round($stats['max'], 2),
            'suggested_price' => $suggestedPrice,
            'resale_price' => $resalePrice,
            'resale_margin' => $resaleMargin,
            'listings_count' => $listingsCount,
            'low_data_warning' => $lowDataWarning,
            'confidence' => $confidence,
            'std_dev' => round($stats['std_dev'], 2),
            'last_collected_at' => $lastCollectedAt,
            'margin' => $margin,
            'battery_modifier' => $batteryMod,
            'device_state_modifier' => $deviceStateMod,
            'accessory_level' => $accessoryLevel,
            'accessory_modifier' => $accessoryMod,
        ];
    }

    public static function resolveAccessoryLevel(array $checks): string
    {
        $count = count(array_filter($checks));

        return match (true) {
            $count === 0 => 'complete',
            $count === 1 => 'partial',
            default => 'none',
        };
    }

    private function fetchListingData(string $model, string $storage, array $cities, int $lookbackDays): array
    {
        $query = DB::connection('dgifipe')
            ->table('market_listings')
            ->where('model', $model)
            ->where('storage', $storage)
            ->whereIn('city', $cities)
            ->where('collected_at', '>=', now()->subDays($lookbackDays))
            ->where('title', 'NOT REGEXP', '(lacrado|lacrada|selado|selada|sealed|novo na caixa|zero na caixa)');

        $lastCollectedAt = (clone $query)->max('collected_at');
        $prices = $query->pluck('price')->sort()->values()->toArray();

        return [
            'prices' => array_map('floatval', $prices),
            'last_collected_at' => $lastCollectedAt,
        ];
    }

    private function secondsUntilNextScrape(): int
    {
        $now = Carbon::now();
        $nextScrape = $now->copy()->setTime(3, 30, 0);

        if ($now->greaterThanOrEqualTo($nextScrape)) {
            $nextScrape->addDay();
        }

        return max((int) $now->diffInSeconds($nextScrape), 60);
    }

    private function calculateConfidence(int $count, float $stdDev, float $average): string
    {
        if ($average <= 0) {
            return 'low';
        }

        $cv = $stdDev / $average;

        if ($count >= 10 && $cv < 0.30) {
            return 'high';
        }

        if ($count >= 5 && $cv < 0.50) {
            return 'medium';
        }

        return 'low';
    }

    private function getBatteryModifier(int $health): float
    {
        $rules = config('dgifipe.default_battery_rules');

        foreach ($rules as $rule) {
            if ($health >= $rule['min'] && $health <= $rule['max']) {
                return (float) $rule['modifier'];
            }
        }

        return -25.0;
    }

    private function getDeviceStateModifier(string $state): float
    {
        $options = config('dgifipe.default_device_state_options');

        return (float) ($options[$state] ?? 0);
    }

    private function getAccessoryModifier(string $level): float
    {
        $options = config('dgifipe.default_accessory_options');

        return (float) ($options[$level] ?? 0);
    }
}
