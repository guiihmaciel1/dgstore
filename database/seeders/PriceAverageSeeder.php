<?php

namespace Database\Seeders;

use App\Domain\Valuation\Models\IphoneModel;
use App\Domain\Valuation\Models\PriceAverage;
use Illuminate\Database\Seeder;

class PriceAverageSeeder extends Seeder
{
    /**
     * Seed de preços de mercado (novos) por modelo e storage.
     *
     * Os preços são pesquisados manualmente no Mercado Livre e atualizados periodicamente.
     * O ValuationService aplica o fator de depreciação por geração para estimar o preço de usado.
     *
     * Para atualizar: edite o array $prices abaixo e rode:
     *   php artisan db:seed --class=PriceAverageSeeder
     */
    public function run(): void
    {
        // Formato: 'slug' => ['storage' => [avg, min, max]]
        // Preços de iPhones NOVOS no Mercado Livre (em R$)
        $prices = [
            // iPhone 11 (2019) — descontinuado, poucos anúncios de novo
            // 'iphone-11' => ['64GB' => [2800, 2500, 3100], '128GB' => [3200, 2900, 3500]],

            // iPhone 12 (2020)
            // 'iphone-12' => ['64GB' => [3200, 2900, 3500], '128GB' => [3500, 3200, 3800]],

            // iPhone 13 (2021)
            'iphone-13' => [
                '128GB' => [3999, 3699, 4299],
                '256GB' => [4499, 4199, 4799],
            ],

            // iPhone 14 (2022)
            'iphone-14' => [
                '128GB' => [4499, 4199, 4799],
                '256GB' => [5199, 4899, 5499],
            ],

            // iPhone 15 (2023)
            'iphone-15' => [
                '128GB' => [5299, 4999, 5599],
                '256GB' => [6099, 5799, 6399],
                '512GB' => [7499, 7199, 7799],
            ],
            'iphone-15-plus' => [
                '128GB' => [5999, 5699, 6299],
                '256GB' => [6799, 6499, 7099],
            ],
            'iphone-15-pro' => [
                '128GB' => [6499, 6199, 6799],
                '256GB' => [7299, 6999, 7599],
                '512GB' => [8799, 8499, 9099],
                '1TB' => [10299, 9999, 10599],
            ],
            'iphone-15-pro-max' => [
                '256GB' => [8499, 8199, 8799],
                '512GB' => [9999, 9699, 10299],
                '1TB' => [11499, 11199, 11799],
            ],

            // iPhone 16 (2024)
            'iphone-16' => [
                '128GB' => [5999, 5699, 6299],
                '256GB' => [6799, 6499, 7099],
                '512GB' => [8199, 7899, 8499],
            ],
            'iphone-16-plus' => [
                '128GB' => [6799, 6499, 7099],
                '256GB' => [7599, 7299, 7899],
            ],
            'iphone-16-pro' => [
                '128GB' => [7499, 7199, 7799],
                '256GB' => [8299, 7999, 8599],
                '512GB' => [9799, 9499, 10099],
                '1TB' => [11299, 10999, 11599],
            ],
            'iphone-16-pro-max' => [
                '256GB' => [9499, 9199, 9799],
                '512GB' => [10999, 10699, 11299],
                '1TB' => [12499, 12199, 12799],
            ],
        ];

        $count = 0;

        foreach ($prices as $slug => $storages) {
            $model = IphoneModel::where('slug', $slug)->first();

            if (! $model) {
                $this->command->warn("Modelo não encontrado: {$slug}");
                continue;
            }

            foreach ($storages as $storage => [$avg, $min, $max]) {
                $median = $avg; // Para simplificar, mediana = média
                $suggestedBuy = round($avg * 0.70, 2); // 30% margem

                PriceAverage::updateOrCreate(
                    [
                        'iphone_model_id' => $model->id,
                        'storage' => $storage,
                        'calculated_at' => now()->toDateString(),
                    ],
                    [
                        'avg_price' => $avg,
                        'median_price' => $median,
                        'min_price' => $min,
                        'max_price' => $max,
                        'suggested_buy_price' => $suggestedBuy,
                        'sample_count' => 1,
                    ],
                );

                $count++;
            }
        }

        $this->command->info("{$count} preços de mercado inseridos/atualizados.");
    }
}
