<?php

namespace Database\Seeders;

use App\Domain\Valuation\Models\IphoneModel;
use Illuminate\Database\Seeder;

/**
 * Seeder para padronizar as cores dos modelos iPhone 17, 17 Pro e 17 Pro Max
 * conforme solicitado pelo usuário.
 */
class StandardizeIphone17ColorsSeeder extends Seeder
{
    public function run(): void
    {
        // iPhone 17 Pro - cores: Deep Blue, Silver, Orange
        IphoneModel::updateOrCreate(
            ['name' => 'iPhone 17 Pro'],
            [
                'slug' => 'iphone-17-pro',
                'search_term' => 'iphone 17 pro',
                'colors' => ['Deep Blue', 'Silver', 'Orange'],
                'storages' => ['256GB', '512GB', '1TB'],
                'active' => true,
                'release_year' => 2025,
            ]
        );

        // iPhone 17 Pro Max - cores: Deep Blue, Silver, Orange
        IphoneModel::updateOrCreate(
            ['name' => 'iPhone 17 Pro Max'],
            [
                'slug' => 'iphone-17-pro-max',
                'search_term' => 'iphone 17 pro max',
                'colors' => ['Deep Blue', 'Silver', 'Orange'],
                'storages' => ['256GB', '512GB', '1TB'],
                'active' => true,
                'release_year' => 2025,
            ]
        );

        // iPhone 17 - cores: Preto, Branco, Verde, Azul, Lavanda
        IphoneModel::updateOrCreate(
            ['name' => 'iPhone 17'],
            [
                'slug' => 'iphone-17',
                'search_term' => 'iphone 17',
                'colors' => ['Preto', 'Branco', 'Verde', 'Azul', 'Lavanda'],
                'storages' => ['128GB', '256GB', '512GB'],
                'active' => true,
                'release_year' => 2025,
            ]
        );

        $this->command->info('✓ Cores dos modelos iPhone 17, 17 Pro e 17 Pro Max padronizadas com sucesso!');
    }
}
