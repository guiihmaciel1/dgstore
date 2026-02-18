<?php

namespace Database\Seeders;

use App\Domain\B2B\Models\B2BProduct;
use App\Domain\B2B\Models\B2BRetailer;
use App\Domain\B2B\Models\B2BSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class B2BSeeder extends Seeder
{
    public function run(): void
    {
        B2BSetting::set('minimum_order_amount', '5000');

        B2BRetailer::firstOrCreate(
            ['email' => 'lojista@teste.com'],
            [
                'store_name' => 'Apple Store Demo',
                'owner_name' => 'João Lojista',
                'document' => '12.345.678/0001-90',
                'whatsapp' => '(11) 99999-0000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'password' => Hash::make('password'),
                'status' => 'approved',
            ]
        );

        // Fotos por modelo (armazenadas em storage/app/public/b2b-products/)
        $photos = [
            'iPhone 17 Pro Max' => 'b2b-products/iphone17promax-1.jpg',
            'iPhone 17 Pro' => 'b2b-products/iphone17pro-1.jpg',
            'iPhone 17' => 'b2b-products/iphone17-1.jpg',
        ];

        // Fotos alternativas para variar entre cores
        $altPhotos = [
            'iPhone 17 Pro Max' => ['b2b-products/iphone17promax-1.jpg', 'b2b-products/iphone17promax-2.jpg', 'b2b-products/iphone17promax-3.jpg'],
            'iPhone 17 Pro' => ['b2b-products/iphone17pro-1.jpg', 'b2b-products/iphone17pro-2.jpg', 'b2b-products/iphone17pro-3.jpg'],
            'iPhone 17' => ['b2b-products/iphone17-1.jpg', 'b2b-products/iphone17-2.jpg', 'b2b-products/iphone17-3.jpg'],
        ];

        $products = [
            // ===== iPhone 17 Pro Max - Silver =====
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '256GB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 7200, 'wholesale_price' => 7900, 'stock_quantity' => 15, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '512GB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 8400, 'wholesale_price' => 9200, 'stock_quantity' => 10, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '1TB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 9600, 'wholesale_price' => 10500, 'stock_quantity' => 5, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '2TB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 11800, 'wholesale_price' => 12900, 'stock_quantity' => 3, 'photo_idx' => 0],
            // ===== iPhone 17 Pro Max - Cosmic Orange =====
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '256GB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 7200, 'wholesale_price' => 7900, 'stock_quantity' => 12, 'photo_idx' => 1],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '512GB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 8400, 'wholesale_price' => 9200, 'stock_quantity' => 8, 'photo_idx' => 1],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '1TB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 9600, 'wholesale_price' => 10500, 'stock_quantity' => 4, 'photo_idx' => 1],
            // ===== iPhone 17 Pro Max - Deep Blue =====
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '256GB', 'color' => 'Deep Blue', 'condition' => 'sealed', 'cost_price' => 7200, 'wholesale_price' => 7900, 'stock_quantity' => 12, 'photo_idx' => 2],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '512GB', 'color' => 'Deep Blue', 'condition' => 'sealed', 'cost_price' => 8400, 'wholesale_price' => 9200, 'stock_quantity' => 6, 'photo_idx' => 2],
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '1TB', 'color' => 'Deep Blue', 'condition' => 'sealed', 'cost_price' => 9600, 'wholesale_price' => 10500, 'stock_quantity' => 3, 'photo_idx' => 2],

            // ===== iPhone 17 Pro - Silver =====
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '256GB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 6200, 'wholesale_price' => 6900, 'stock_quantity' => 20, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '512GB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 7400, 'wholesale_price' => 8100, 'stock_quantity' => 12, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '1TB', 'color' => 'Silver', 'condition' => 'sealed', 'cost_price' => 8600, 'wholesale_price' => 9400, 'stock_quantity' => 6, 'photo_idx' => 0],
            // ===== iPhone 17 Pro - Cosmic Orange =====
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '256GB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 6200, 'wholesale_price' => 6900, 'stock_quantity' => 18, 'photo_idx' => 1],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '512GB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 7400, 'wholesale_price' => 8100, 'stock_quantity' => 10, 'photo_idx' => 1],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '1TB', 'color' => 'Cosmic Orange', 'condition' => 'sealed', 'cost_price' => 8600, 'wholesale_price' => 9400, 'stock_quantity' => 4, 'photo_idx' => 1],
            // ===== iPhone 17 Pro - Deep Blue =====
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '256GB', 'color' => 'Deep Blue', 'condition' => 'sealed', 'cost_price' => 6200, 'wholesale_price' => 6900, 'stock_quantity' => 15, 'photo_idx' => 2],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '512GB', 'color' => 'Deep Blue', 'condition' => 'sealed', 'cost_price' => 7400, 'wholesale_price' => 8100, 'stock_quantity' => 8, 'photo_idx' => 2],

            // ===== iPhone 17 - Black =====
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'Black', 'condition' => 'sealed', 'cost_price' => 4800, 'wholesale_price' => 5400, 'stock_quantity' => 30, 'photo_idx' => 0],
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '512GB', 'color' => 'Black', 'condition' => 'sealed', 'cost_price' => 5800, 'wholesale_price' => 6400, 'stock_quantity' => 15, 'photo_idx' => 0],
            // ===== iPhone 17 - White =====
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'White', 'condition' => 'sealed', 'cost_price' => 4800, 'wholesale_price' => 5400, 'stock_quantity' => 25, 'photo_idx' => 1],
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '512GB', 'color' => 'White', 'condition' => 'sealed', 'cost_price' => 5800, 'wholesale_price' => 6400, 'stock_quantity' => 12, 'photo_idx' => 1],
            // ===== iPhone 17 - Mist Blue =====
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'Mist Blue', 'condition' => 'sealed', 'cost_price' => 4800, 'wholesale_price' => 5400, 'stock_quantity' => 20, 'photo_idx' => 2],
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '512GB', 'color' => 'Mist Blue', 'condition' => 'sealed', 'cost_price' => 5800, 'wholesale_price' => 6400, 'stock_quantity' => 10, 'photo_idx' => 2],
            // ===== iPhone 17 - Sage =====
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'Sage', 'condition' => 'sealed', 'cost_price' => 4800, 'wholesale_price' => 5400, 'stock_quantity' => 18, 'photo_idx' => 0],
            // ===== iPhone 17 - Lavender =====
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'Lavender', 'condition' => 'sealed', 'cost_price' => 4800, 'wholesale_price' => 5400, 'stock_quantity' => 18, 'photo_idx' => 1],

            // ===== Semi-novos (seleção) =====
            ['name' => 'iPhone 17 Pro Max', 'model' => 'iPhone 17 Pro Max', 'storage' => '256GB', 'color' => 'Silver', 'condition' => 'semi_new', 'cost_price' => 5800, 'wholesale_price' => 6600, 'stock_quantity' => 5, 'photo_idx' => 0],
            ['name' => 'iPhone 17 Pro', 'model' => 'iPhone 17 Pro', 'storage' => '256GB', 'color' => 'Silver', 'condition' => 'semi_new', 'cost_price' => 4900, 'wholesale_price' => 5600, 'stock_quantity' => 7, 'photo_idx' => 0],
            ['name' => 'iPhone 17', 'model' => 'iPhone 17', 'storage' => '256GB', 'color' => 'Black', 'condition' => 'semi_new', 'cost_price' => 3500, 'wholesale_price' => 4100, 'stock_quantity' => 10, 'photo_idx' => 2],
        ];

        foreach ($products as $index => $product) {
            $photoIdx = $product['photo_idx'];
            unset($product['photo_idx']);

            $modelPhotos = $altPhotos[$product['model']] ?? [];
            $photo = $modelPhotos[$photoIdx] ?? $photos[$product['model']] ?? null;

            B2BProduct::updateOrCreate(
                ['model' => $product['model'], 'storage' => $product['storage'], 'color' => $product['color'], 'condition' => $product['condition']],
                array_merge($product, ['sort_order' => $index, 'active' => true, 'photo' => $photo])
            );
        }

        $this->command->info('Dados B2B criados: configurações, lojista demo, ' . count($products) . ' produtos iPhone 17.');
    }
}
