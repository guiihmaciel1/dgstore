<?php

namespace Database\Seeders;

use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Enums\ProductCategory;
use App\Domain\Product\Enums\ProductCondition;
use App\Domain\Product\Models\Product;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@dgstore.com.br',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'active' => true,
        ]);

        // Criar usuário vendedor
        $seller = User::create([
            'name' => 'Vendedor Demo',
            'email' => 'vendedor@dgstore.com.br',
            'password' => Hash::make('password'),
            'role' => UserRole::Seller,
            'active' => true,
        ]);

        $this->command->info('Usuários criados:');
        $this->command->info('  - Admin: admin@dgstore.com.br / password');
        $this->command->info('  - Vendedor: vendedor@dgstore.com.br / password');

        // Criar clientes de exemplo
        $customers = [
            ['name' => 'João Silva', 'phone' => '11999887766', 'email' => 'joao@email.com', 'cpf' => '12345678901'],
            ['name' => 'Maria Santos', 'phone' => '11988776655', 'email' => 'maria@email.com', 'cpf' => '23456789012'],
            ['name' => 'Pedro Oliveira', 'phone' => '11977665544', 'email' => null, 'cpf' => null],
            ['name' => 'Ana Costa', 'phone' => '11966554433', 'email' => 'ana@email.com', 'cpf' => '34567890123'],
            ['name' => 'Carlos Ferreira', 'phone' => '11955443322', 'email' => null, 'cpf' => '45678901234'],
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }

        $this->command->info('5 clientes de exemplo criados.');

        // Criar produtos de exemplo - iPhones
        $iphones = [
            [
                'name' => 'iPhone 15 Pro Max',
                'sku' => 'IPH15PM256BK',
                'category' => ProductCategory::Iphone,
                'model' => '15 Pro Max',
                'storage' => '256GB',
                'color' => 'Preto Titânio',
                'condition' => ProductCondition::New,
                'imei' => '352345678901234',
                'cost_price' => 7500.00,
                'sale_price' => 9499.00,
                'stock_quantity' => 5,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IPH15P128WH',
                'category' => ProductCategory::Iphone,
                'model' => '15 Pro',
                'storage' => '128GB',
                'color' => 'Branco Titânio',
                'condition' => ProductCondition::New,
                'imei' => '352345678901235',
                'cost_price' => 6500.00,
                'sale_price' => 8299.00,
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'iPhone 15',
                'sku' => 'IPH15128BL',
                'category' => ProductCategory::Iphone,
                'model' => '15',
                'storage' => '128GB',
                'color' => 'Azul',
                'condition' => ProductCondition::New,
                'imei' => '352345678901236',
                'cost_price' => 4500.00,
                'sale_price' => 5999.00,
                'stock_quantity' => 10,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'iPhone 14 Pro Max',
                'sku' => 'IPH14PM256PR',
                'category' => ProductCategory::Iphone,
                'model' => '14 Pro Max',
                'storage' => '256GB',
                'color' => 'Roxo Profundo',
                'condition' => ProductCondition::Used,
                'imei' => '352345678901237',
                'cost_price' => 4800.00,
                'sale_price' => 6499.00,
                'stock_quantity' => 3,
                'min_stock_alert' => 1,
            ],
            [
                'name' => 'iPhone 13',
                'sku' => 'IPH13128PK',
                'category' => ProductCategory::Iphone,
                'model' => '13',
                'storage' => '128GB',
                'color' => 'Rosa',
                'condition' => ProductCondition::Refurbished,
                'imei' => '352345678901238',
                'cost_price' => 2800.00,
                'sale_price' => 3999.00,
                'stock_quantity' => 6,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'iPhone 12 Mini',
                'sku' => 'IPH12M64GR',
                'category' => ProductCategory::Iphone,
                'model' => '12 Mini',
                'storage' => '64GB',
                'color' => 'Verde',
                'condition' => ProductCondition::Used,
                'imei' => '352345678901239',
                'cost_price' => 1800.00,
                'sale_price' => 2499.00,
                'stock_quantity' => 2,
                'min_stock_alert' => 1,
            ],
        ];

        foreach ($iphones as $iphoneData) {
            Product::create($iphoneData);
        }

        $this->command->info('6 iPhones de exemplo criados.');

        // Criar acessórios
        $accessories = [
            [
                'name' => 'Carregador USB-C 20W Apple Original',
                'sku' => 'ACC20WUSBC',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 120.00,
                'sale_price' => 199.00,
                'stock_quantity' => 25,
                'min_stock_alert' => 5,
            ],
            [
                'name' => 'Cabo Lightning USB-C 1m Apple',
                'sku' => 'ACCCABLELT1M',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 80.00,
                'sale_price' => 149.00,
                'stock_quantity' => 30,
                'min_stock_alert' => 10,
            ],
            [
                'name' => 'AirPods Pro 2ª Geração',
                'sku' => 'ACCAIRPODSP2',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 1200.00,
                'sale_price' => 1899.00,
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Capa Silicone iPhone 15 Pro Max',
                'sku' => 'ACCCAPA15PM',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 80.00,
                'sale_price' => 149.00,
                'stock_quantity' => 20,
                'min_stock_alert' => 5,
            ],
            [
                'name' => 'Película de Vidro iPhone 15',
                'sku' => 'ACCPELVD15',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 15.00,
                'sale_price' => 49.90,
                'stock_quantity' => 50,
                'min_stock_alert' => 15,
            ],
            [
                'name' => 'MagSafe Charger Apple',
                'sku' => 'ACCMAGSAFE',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 200.00,
                'sale_price' => 399.00,
                'stock_quantity' => 12,
                'min_stock_alert' => 3,
            ],
        ];

        foreach ($accessories as $accessoryData) {
            Product::create($accessoryData);
        }

        $this->command->info('6 acessórios de exemplo criados.');

        // Criar serviços
        $services = [
            [
                'name' => 'Troca de Tela iPhone 15 Pro Max',
                'sku' => 'SRVTELA15PM',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 800.00,
                'sale_price' => 1500.00,
                'stock_quantity' => 99,
                'min_stock_alert' => 0,
            ],
            [
                'name' => 'Troca de Bateria iPhone',
                'sku' => 'SRVBATERIA',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 150.00,
                'sale_price' => 350.00,
                'stock_quantity' => 99,
                'min_stock_alert' => 0,
            ],
            [
                'name' => 'Backup e Transferência de Dados',
                'sku' => 'SRVBACKUP',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 0.00,
                'sale_price' => 80.00,
                'stock_quantity' => 99,
                'min_stock_alert' => 0,
            ],
        ];

        foreach ($services as $serviceData) {
            Product::create($serviceData);
        }

        $this->command->info('3 serviços de exemplo criados.');
        $this->command->info('');
        $this->command->info('Seeding concluído com sucesso!');
    }
}
