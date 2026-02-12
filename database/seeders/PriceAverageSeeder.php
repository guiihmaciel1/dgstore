<?php

namespace Database\Seeders;

use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\PriceAverage;
use Illuminate\Database\Seeder;

class PriceAverageSeeder extends Seeder
{
    /**
     * Seed de preços de mercado (USADOS) por modelo e storage.
     *
     * Preços pesquisados manualmente no Facebook Marketplace — região SJRP e interior SP.
     * Última atualização: 10/02/2026 (18 capturas analisadas, ~220 anúncios)
     *
     * Para atualizar: edite o array $prices abaixo e rode:
     *   php artisan db:seed --class=PriceAverageSeeder
     */
    public function run(): void
    {
        // Formato: 'slug' => ['storage' => [avg, min, max, samples]]
        // Preços de iPhones USADOS no marketplace (em R$)
        $prices = [
            // ── iPhone 11 (2019) ──────────────────────
            'iphone-11' => [
                '64GB'  => [943, 680, 1200, 25],
                '128GB' => [1025, 680, 1300, 13],
            ],
            'iphone-11-pro' => [
                '64GB'  => [1143, 950, 1399, 7],
                '256GB' => [1500, 1400, 1600, 3],
            ],
            'iphone-11-pro-max' => [
                '64GB'  => [1362, 1050, 1500, 9],
                '256GB' => [1700, 1700, 1700, 1],
            ],

            // ── iPhone 12 (2020) ──────────────────────
            'iphone-12' => [
                '64GB'  => [925, 850, 1000, 2],
                '128GB' => [1449, 1299, 1599, 2],
            ],
            'iphone-12-mini' => [
                '128GB' => [1450, 1450, 1450, 1],
            ],
            'iphone-12-pro' => [
                '128GB' => [1845, 1600, 2200, 6],
            ],
            'iphone-12-pro-max' => [
                '128GB' => [2183, 1800, 2499, 6],
                '256GB' => [1800, 1800, 1800, 2],
            ],

            // ── iPhone 13 (2021) ──────────────────────
            'iphone-13' => [
                '128GB' => [2031, 1600, 2900, 33],
                '256GB' => [2153, 2059, 2299, 3],
            ],
            'iphone-13-mini' => [
                '128GB' => [1897, 1897, 1897, 1],
            ],
            'iphone-13-pro' => [
                '128GB' => [2442, 2000, 2750, 5],
            ],
            'iphone-13-pro-max' => [
                '128GB' => [2644, 1600, 3000, 8],
                '256GB' => [3145, 2990, 3299, 2],
            ],

            // ── iPhone 14 (2022) ──────────────────────
            'iphone-14' => [
                '128GB' => [2576, 1900, 3000, 14],
                '256GB' => [2799, 2799, 2799, 1],
            ],
            'iphone-14-plus' => [
                '128GB' => [2927, 2300, 3400, 7],
            ],
            'iphone-14-pro' => [
                '128GB' => [3097, 2900, 3390, 3],
                '256GB' => [3800, 3800, 3800, 1],
            ],
            'iphone-14-pro-max' => [
                '128GB' => [3511, 2990, 4199, 8],
                '256GB' => [3810, 3499, 4100, 7],
            ],

            // ── iPhone 15 (2023) ──────────────────────
            'iphone-15' => [
                '128GB' => [3119, 2200, 3700, 19],
                '256GB' => [3700, 3700, 3700, 1],
            ],
            'iphone-15-plus' => [
                '128GB' => [3600, 3450, 3799, 4],
            ],
            'iphone-15-pro' => [
                '128GB' => [4050, 3750, 4500, 5],
                '256GB' => [4075, 3899, 4250, 2],
                '512GB' => [4400, 4400, 4400, 1],
            ],
            'iphone-15-pro-max' => [
                '256GB' => [4542, 3850, 5250, 9],
            ],

            // ── iPhone 16 (2024) ──────────────────────
            'iphone-16' => [
                '128GB' => [4112, 3300, 4999, 8],
            ],
            'iphone-16-plus' => [
                '128GB' => [4699, 4699, 4699, 1],
            ],
            'iphone-16-pro' => [
                '128GB' => [5075, 4500, 5899, 10],
                '256GB' => [5187, 4800, 5700, 4],
            ],
            'iphone-16-pro-max' => [
                '256GB' => [5898, 5499, 6300, 8],
            ],
        ];

        $count = 0;

        foreach ($prices as $slug => $storages) {
            $model = IphoneModel::where('slug', $slug)->first();

            if (! $model) {
                $this->command->warn("Modelo não encontrado: {$slug}");
                continue;
            }

            foreach ($storages as $storage => [$avg, $min, $max, $samples]) {
                $suggestedBuy = round($avg * 0.70, 2); // 30% margem

                PriceAverage::updateOrCreate(
                    [
                        'iphone_model_id' => $model->id,
                        'storage' => $storage,
                        'calculated_at' => now()->toDateString(),
                    ],
                    [
                        'avg_price' => $avg,
                        'median_price' => $avg,
                        'min_price' => $min,
                        'max_price' => $max,
                        'suggested_buy_price' => $suggestedBuy,
                        'sample_count' => $samples,
                    ],
                );

                $count++;
            }
        }

        $this->command->info("{$count} preços de mercado (usados) inseridos/atualizados.");
    }
}
