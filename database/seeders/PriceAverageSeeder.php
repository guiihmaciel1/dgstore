<?php

namespace Database\Seeders;

use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\PriceAverage;
use Illuminate\Database\Seeder;

class PriceAverageSeeder extends Seeder
{
    /**
     * Seed de preços de mercado por modelo e storage.
     *
     * Preços pesquisados manualmente no Facebook Marketplace — região SJRP e interior SP.
     * Última atualização: 15/02/2026 (22 capturas analisadas, ~260 anúncios)
     *
     * Para atualizar: edite o array $prices abaixo e rode:
     *   php artisan db:seed --class=PriceAverageSeeder
     */
    public function run(): void
    {
        // Formato: 'slug' => ['storage' => [avg, min, max, samples]]
        // Preços de iPhones no marketplace (em R$)
        $prices = [
            // ── iPhone 11 (2019) ─── usados ──────────
            'iphone-11' => [
                '64GB'  => [943, 680, 1200, 25],
                '128GB' => [1025, 680, 1300, 13],
            ],
            'iphone-11-pro' => [
                '64GB'  => [1090, 800, 1399, 10],   // +3 amostras (R$1.000, R$1.100, R$800)
                '256GB' => [1475, 1399, 1600, 4],    // +1 amostra (R$1.399)
            ],
            'iphone-11-pro-max' => [
                '64GB'  => [1362, 1050, 1500, 9],
                '256GB' => [1520, 1380, 1700, 9],    // +8 amostras (R$1.400~R$1.700)
                '512GB' => [1499, 1499, 1499, 1],    // novo: R$1.499 seminovo c/ garantia
            ],

            // ── iPhone 12 (2020) ─── usados ──────────
            'iphone-12' => [
                '64GB'  => [925, 850, 1000, 2],
                '128GB' => [1449, 1299, 1599, 2],
            ],
            'iphone-12-mini' => [
                '128GB' => [1450, 1450, 1450, 1],
            ],
            'iphone-12-pro' => [
                '128GB' => [1844, 1600, 2200, 8],    // +2 amostras (R$1.890, R$1.790)
                '256GB' => [1875, 1850, 1900, 2],    // novo: R$1.850, R$1.900
            ],
            'iphone-12-pro-max' => [
                '128GB' => [2185, 1800, 2499, 7],    // +1 amostra (R$2.199)
                '256GB' => [1900, 1800, 2100, 3],    // +1 amostra (R$2.100)
            ],

            // ── iPhone 13 (2021) ─── usados ──────────
            'iphone-13' => [
                '128GB' => [2031, 1600, 2900, 33],
                '256GB' => [2153, 2059, 2299, 3],
            ],
            'iphone-13-mini' => [
                '128GB' => [1897, 1897, 1897, 1],
            ],
            'iphone-13-pro' => [
                '128GB' => [2368, 2000, 2750, 6],    // +1 amostra (R$2.000)
            ],
            'iphone-13-pro-max' => [
                '128GB' => [2644, 1600, 3000, 8],
                '256GB' => [3145, 2990, 3299, 2],
            ],

            // ── iPhone 14 (2022) ─── usados ──────────
            'iphone-14' => [
                '128GB' => [2576, 1900, 3000, 14],
                '256GB' => [2799, 2799, 2799, 1],
            ],
            'iphone-14-plus' => [
                '128GB' => [2927, 2300, 3400, 7],
            ],
            'iphone-14-pro' => [
                '128GB' => [2998, 2700, 3390, 4],    // +1 amostra (R$2.700)
                '256GB' => [3300, 2800, 3800, 4],    // +3 amostras (R$2.800, R$3.000, R$3.600)
            ],
            'iphone-14-pro-max' => [
                '128GB' => [3511, 2990, 4199, 8],
                '256GB' => [3810, 3499, 4100, 7],
            ],

            // ── iPhone 15 (2023) ─── usados ──────────
            'iphone-15' => [
                '128GB' => [3119, 2200, 3700, 19],
                '256GB' => [3700, 3700, 3700, 1],
            ],
            'iphone-15-plus' => [
                '128GB' => [3600, 3450, 3799, 4],
            ],
            'iphone-15-pro' => [
                '128GB' => [4050, 3750, 4500, 5],
                '256GB' => [4059, 3899, 4250, 5],    // +3 amostras (R$3.899, R$3.999, R$4.250)
                '512GB' => [4400, 4400, 4400, 1],
            ],
            'iphone-15-pro-max' => [
                '256GB' => [4538, 3850, 5250, 10],   // +1 amostra (R$4.500)
            ],

            // ── iPhone 16 (2024) ─── usados ──────────
            'iphone-16' => [
                '128GB' => [4112, 3300, 4999, 8],
            ],
            'iphone-16-plus' => [
                '128GB' => [4699, 4699, 4699, 1],
            ],
            'iphone-16-pro' => [
                '128GB' => [5075, 4500, 5899, 10],
                '256GB' => [5110, 4800, 5700, 5],    // +1 amostra (R$4.800)
            ],
            'iphone-16-pro-max' => [
                '256GB' => [5894, 5200, 6500, 11],   // +3 amostras (R$5.200, R$5.950, R$6.500)
            ],

            // ── iPhone 17 (2025) ─── novos/lacrados ──
            'iphone-17-pro' => [
                '256GB' => [7775, 7550, 7999, 2],    // novo: R$7.550, R$7.999 (lacrados)
            ],
            'iphone-17-pro-max' => [
                '256GB' => [7833, 7500, 7999, 3],    // novo: R$7.500, R$7.999, R$7.999 (lacrados)
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
