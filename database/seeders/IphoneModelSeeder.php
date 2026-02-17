<?php

namespace Database\Seeders;

use App\Domain\Valuation\Models\IphoneModel;
use Illuminate\Database\Seeder;

class IphoneModelSeeder extends Seeder
{
    /**
     * Seed do catálogo de modelos de iPhone para rastreamento de preços.
     */
    public function run(): void
    {
        $models = [
            // iPhone 11 (2019)
            [
                'name' => 'iPhone 11',
                'slug' => 'iphone-11',
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Preto', 'Branco', 'Verde', 'Amarelo', 'Roxo', 'Vermelho'],
                'search_term' => 'iphone 11',
                'release_year' => 2019,
            ],
            [
                'name' => 'iPhone 11 Pro',
                'slug' => 'iphone-11-pro',
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Cinza Espacial', 'Prateado', 'Dourado', 'Verde Meia-noite'],
                'search_term' => 'iphone 11 pro',
                'release_year' => 2019,
            ],
            [
                'name' => 'iPhone 11 Pro Max',
                'slug' => 'iphone-11-pro-max',
                'storages' => ['64GB', '256GB', '512GB'],
                'colors' => ['Cinza Espacial', 'Prateado', 'Dourado', 'Verde Meia-noite'],
                'search_term' => 'iphone 11 pro max',
                'release_year' => 2019,
            ],

            // iPhone 12 (2020)
            [
                'name' => 'iPhone 12',
                'slug' => 'iphone-12',
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Preto', 'Branco', 'Azul', 'Verde', 'Vermelho'],
                'search_term' => 'iphone 12',
                'release_year' => 2020,
            ],
            [
                'name' => 'iPhone 12 Mini',
                'slug' => 'iphone-12-mini',
                'storages' => ['64GB', '128GB', '256GB'],
                'colors' => ['Preto', 'Branco', 'Azul', 'Verde', 'Vermelho'],
                'search_term' => 'iphone 12 mini',
                'release_year' => 2020,
            ],
            [
                'name' => 'iPhone 12 Pro',
                'slug' => 'iphone-12-pro',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Grafite', 'Prateado', 'Dourado', 'Azul Pacífico'],
                'search_term' => 'iphone 12 pro',
                'release_year' => 2020,
            ],
            [
                'name' => 'iPhone 12 Pro Max',
                'slug' => 'iphone-12-pro-max',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Grafite', 'Prateado', 'Dourado', 'Azul Pacífico'],
                'search_term' => 'iphone 12 pro max',
                'release_year' => 2020,
            ],

            // iPhone 13 (2021)
            [
                'name' => 'iPhone 13',
                'slug' => 'iphone-13',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Meia-noite', 'Estelar', 'Azul', 'Rosa', 'Vermelho', 'Verde'],
                'search_term' => 'iphone 13',
                'release_year' => 2021,
            ],
            [
                'name' => 'iPhone 13 Mini',
                'slug' => 'iphone-13-mini',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Meia-noite', 'Estelar', 'Azul', 'Rosa', 'Vermelho', 'Verde'],
                'search_term' => 'iphone 13 mini',
                'release_year' => 2021,
            ],
            [
                'name' => 'iPhone 13 Pro',
                'slug' => 'iphone-13-pro',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Grafite', 'Prateado', 'Dourado', 'Azul Sierra'],
                'search_term' => 'iphone 13 pro',
                'release_year' => 2021,
            ],
            [
                'name' => 'iPhone 13 Pro Max',
                'slug' => 'iphone-13-pro-max',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Grafite', 'Prateado', 'Dourado', 'Azul Sierra'],
                'search_term' => 'iphone 13 pro max',
                'release_year' => 2021,
            ],

            // iPhone 14 (2022)
            [
                'name' => 'iPhone 14',
                'slug' => 'iphone-14',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Meia-noite', 'Estelar', 'Azul', 'Roxo', 'Vermelho'],
                'search_term' => 'iphone 14',
                'release_year' => 2022,
            ],
            [
                'name' => 'iPhone 14 Plus',
                'slug' => 'iphone-14-plus',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Meia-noite', 'Estelar', 'Azul', 'Roxo', 'Vermelho'],
                'search_term' => 'iphone 14 plus',
                'release_year' => 2022,
            ],
            [
                'name' => 'iPhone 14 Pro',
                'slug' => 'iphone-14-pro',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Preto Espacial', 'Prateado', 'Dourado', 'Roxo Profundo'],
                'search_term' => 'iphone 14 pro',
                'release_year' => 2022,
            ],
            [
                'name' => 'iPhone 14 Pro Max',
                'slug' => 'iphone-14-pro-max',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Preto Espacial', 'Prateado', 'Dourado', 'Roxo Profundo'],
                'search_term' => 'iphone 14 pro max',
                'release_year' => 2022,
            ],

            // iPhone 15 (2023)
            [
                'name' => 'iPhone 15',
                'slug' => 'iphone-15',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Preto', 'Azul', 'Verde', 'Amarelo', 'Rosa'],
                'search_term' => 'iphone 15',
                'release_year' => 2023,
            ],
            [
                'name' => 'iPhone 15 Plus',
                'slug' => 'iphone-15-plus',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Preto', 'Azul', 'Verde', 'Amarelo', 'Rosa'],
                'search_term' => 'iphone 15 plus',
                'release_year' => 2023,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Preto Titânio', 'Branco Titânio', 'Natural Titânio', 'Azul Titânio'],
                'search_term' => 'iphone 15 pro',
                'release_year' => 2023,
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Preto Titânio', 'Branco Titânio', 'Natural Titânio', 'Azul Titânio'],
                'search_term' => 'iphone 15 pro max',
                'release_year' => 2023,
            ],

            // iPhone 16 (2024)
            [
                'name' => 'iPhone 16',
                'slug' => 'iphone-16',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Preto', 'Branco', 'Rosa', 'Verde-azulado', 'Ultramarino'],
                'search_term' => 'iphone 16',
                'release_year' => 2024,
            ],
            [
                'name' => 'iPhone 16 Plus',
                'slug' => 'iphone-16-plus',
                'storages' => ['128GB', '256GB', '512GB'],
                'colors' => ['Preto', 'Branco', 'Rosa', 'Verde-azulado', 'Ultramarino'],
                'search_term' => 'iphone 16 plus',
                'release_year' => 2024,
            ],
            [
                'name' => 'iPhone 16 Pro',
                'slug' => 'iphone-16-pro',
                'storages' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Titânio Preto', 'Titânio Branco', 'Titânio Natural', 'Titânio Deserto'],
                'search_term' => 'iphone 16 pro',
                'release_year' => 2024,
            ],
            [
                'name' => 'iPhone 16 Pro Max',
                'slug' => 'iphone-16-pro-max',
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Titânio Preto', 'Titânio Branco', 'Titânio Natural', 'Titânio Deserto'],
                'search_term' => 'iphone 16 pro max',
                'release_year' => 2024,
            ],

            // iPhone 17 (2025)
            [
                'name' => 'iPhone 17 Pro',
                'slug' => 'iphone-17-pro',
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Titânio Preto', 'Titânio Branco', 'Titânio Natural', 'Titânio Deserto'],
                'search_term' => 'iphone 17 pro',
                'release_year' => 2025,
            ],
            [
                'name' => 'iPhone 17 Pro Max',
                'slug' => 'iphone-17-pro-max',
                'storages' => ['256GB', '512GB', '1TB'],
                'colors' => ['Titânio Preto', 'Titânio Branco', 'Titânio Natural', 'Titânio Deserto'],
                'search_term' => 'iphone 17 pro max',
                'release_year' => 2025,
            ],
        ];

        foreach ($models as $modelData) {
            IphoneModel::firstOrCreate(
                ['slug' => $modelData['slug']],
                $modelData,
            );
        }

        $this->command->info('Catálogo de ' . count($models) . ' modelos de iPhone criado.');
    }
}
