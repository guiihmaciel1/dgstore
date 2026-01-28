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

        // ========================================
        // SMARTPHONES
        // ========================================
        $smartphones = [
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'sku' => 'SPH-IPH15PM256BK',
                'category' => ProductCategory::Smartphone,
                'model' => 'iPhone 15 Pro Max',
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
                'name' => 'iPhone 15 Pro 128GB',
                'sku' => 'SPH-IPH15P128WH',
                'category' => ProductCategory::Smartphone,
                'model' => 'iPhone 15 Pro',
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
                'name' => 'Samsung Galaxy S24 Ultra',
                'sku' => 'SPH-SGS24U256',
                'category' => ProductCategory::Smartphone,
                'model' => 'Galaxy S24 Ultra',
                'storage' => '256GB',
                'color' => 'Violet',
                'condition' => ProductCondition::New,
                'imei' => '352345678901240',
                'cost_price' => 5800.00,
                'sale_price' => 7999.00,
                'stock_quantity' => 6,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'sku' => 'SPH-SGS24128',
                'category' => ProductCategory::Smartphone,
                'model' => 'Galaxy S24',
                'storage' => '128GB',
                'color' => 'Preto',
                'condition' => ProductCondition::New,
                'imei' => '352345678901241',
                'cost_price' => 3200.00,
                'sale_price' => 4499.00,
                'stock_quantity' => 10,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'sku' => 'SPH-XI14U512',
                'category' => ProductCategory::Smartphone,
                'model' => 'Xiaomi 14 Ultra',
                'storage' => '512GB',
                'color' => 'Preto',
                'condition' => ProductCondition::New,
                'imei' => '352345678901242',
                'cost_price' => 4500.00,
                'sale_price' => 6299.00,
                'stock_quantity' => 4,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Motorola Edge 40 Pro',
                'sku' => 'SPH-MOTE40P',
                'category' => ProductCategory::Smartphone,
                'model' => 'Edge 40 Pro',
                'storage' => '256GB',
                'color' => 'Azul',
                'condition' => ProductCondition::New,
                'imei' => '352345678901243',
                'cost_price' => 2800.00,
                'sale_price' => 3999.00,
                'stock_quantity' => 7,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'iPhone 14 Pro Max (Seminovo)',
                'sku' => 'SPH-IPH14PMU',
                'category' => ProductCategory::Smartphone,
                'model' => 'iPhone 14 Pro Max',
                'storage' => '256GB',
                'color' => 'Roxo Profundo',
                'condition' => ProductCondition::Used,
                'imei' => '352345678901237',
                'cost_price' => 4800.00,
                'sale_price' => 6499.00,
                'stock_quantity' => 3,
                'min_stock_alert' => 1,
            ],
        ];

        foreach ($smartphones as $data) {
            Product::create($data);
        }
        $this->command->info('7 smartphones criados.');

        // ========================================
        // ELETRÔNICOS - TABLETS
        // ========================================
        $tablets = [
            [
                'name' => 'iPad Pro 12.9" M2 256GB',
                'sku' => 'TBL-IPADP12M2',
                'category' => ProductCategory::Tablet,
                'model' => 'iPad Pro 12.9"',
                'storage' => '256GB',
                'color' => 'Cinza Espacial',
                'condition' => ProductCondition::New,
                'cost_price' => 7200.00,
                'sale_price' => 9499.00,
                'stock_quantity' => 4,
                'min_stock_alert' => 1,
            ],
            [
                'name' => 'iPad Air 5ª Geração',
                'sku' => 'TBL-IPADAIR5',
                'category' => ProductCategory::Tablet,
                'model' => 'iPad Air 5',
                'storage' => '64GB',
                'color' => 'Azul',
                'condition' => ProductCondition::New,
                'cost_price' => 3800.00,
                'sale_price' => 5199.00,
                'stock_quantity' => 6,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Samsung Galaxy Tab S9 Ultra',
                'sku' => 'TBL-SGTABS9U',
                'category' => ProductCategory::Tablet,
                'model' => 'Galaxy Tab S9 Ultra',
                'storage' => '256GB',
                'color' => 'Grafite',
                'condition' => ProductCondition::New,
                'cost_price' => 5500.00,
                'sale_price' => 7499.00,
                'stock_quantity' => 3,
                'min_stock_alert' => 1,
            ],
        ];

        foreach ($tablets as $data) {
            Product::create($data);
        }
        $this->command->info('3 tablets criados.');

        // ========================================
        // ELETRÔNICOS - NOTEBOOKS
        // ========================================
        $notebooks = [
            [
                'name' => 'MacBook Pro 14" M3 Pro',
                'sku' => 'NTB-MBPM3P14',
                'category' => ProductCategory::Notebook,
                'model' => 'MacBook Pro 14"',
                'storage' => '512GB SSD',
                'color' => 'Cinza Espacial',
                'condition' => ProductCondition::New,
                'cost_price' => 12000.00,
                'sale_price' => 15999.00,
                'stock_quantity' => 3,
                'min_stock_alert' => 1,
            ],
            [
                'name' => 'MacBook Air 15" M3',
                'sku' => 'NTB-MBAM315',
                'category' => ProductCategory::Notebook,
                'model' => 'MacBook Air 15"',
                'storage' => '256GB SSD',
                'color' => 'Meia-noite',
                'condition' => ProductCondition::New,
                'cost_price' => 8500.00,
                'sale_price' => 11499.00,
                'stock_quantity' => 5,
                'min_stock_alert' => 2,
            ],
        ];

        foreach ($notebooks as $data) {
            Product::create($data);
        }
        $this->command->info('2 notebooks criados.');

        // ========================================
        // ELETRÔNICOS - SMARTWATCHES
        // ========================================
        $smartwatches = [
            [
                'name' => 'Apple Watch Ultra 2',
                'sku' => 'SWT-AWULTRA2',
                'category' => ProductCategory::Smartwatch,
                'model' => 'Watch Ultra 2',
                'color' => 'Titânio Natural',
                'condition' => ProductCondition::New,
                'cost_price' => 5200.00,
                'sale_price' => 6999.00,
                'stock_quantity' => 4,
                'min_stock_alert' => 1,
            ],
            [
                'name' => 'Apple Watch Series 9 45mm',
                'sku' => 'SWT-AWS945',
                'category' => ProductCategory::Smartwatch,
                'model' => 'Watch Series 9',
                'color' => 'Preto',
                'condition' => ProductCondition::New,
                'cost_price' => 2800.00,
                'sale_price' => 3999.00,
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Samsung Galaxy Watch 6 Classic',
                'sku' => 'SWT-SGW6C',
                'category' => ProductCategory::Smartwatch,
                'model' => 'Galaxy Watch 6 Classic',
                'color' => 'Prata',
                'condition' => ProductCondition::New,
                'cost_price' => 1800.00,
                'sale_price' => 2699.00,
                'stock_quantity' => 6,
                'min_stock_alert' => 2,
            ],
        ];

        foreach ($smartwatches as $data) {
            Product::create($data);
        }
        $this->command->info('3 smartwatches criados.');

        // ========================================
        // ELETRÔNICOS - FONES DE OUVIDO
        // ========================================
        $headphones = [
            [
                'name' => 'AirPods Pro 2ª Geração',
                'sku' => 'HPH-AIRPODSP2',
                'category' => ProductCategory::Headphone,
                'model' => 'AirPods Pro 2',
                'color' => 'Branco',
                'condition' => ProductCondition::New,
                'cost_price' => 1200.00,
                'sale_price' => 1899.00,
                'stock_quantity' => 12,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'AirPods Max',
                'sku' => 'HPH-AIRPODSMAX',
                'category' => ProductCategory::Headphone,
                'model' => 'AirPods Max',
                'color' => 'Cinza Espacial',
                'condition' => ProductCondition::New,
                'cost_price' => 3200.00,
                'sale_price' => 4499.00,
                'stock_quantity' => 4,
                'min_stock_alert' => 1,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'sku' => 'HPH-SONYWH5',
                'category' => ProductCategory::Headphone,
                'model' => 'WH-1000XM5',
                'color' => 'Preto',
                'condition' => ProductCondition::New,
                'cost_price' => 1800.00,
                'sale_price' => 2599.00,
                'stock_quantity' => 6,
                'min_stock_alert' => 2,
            ],
            [
                'name' => 'Samsung Galaxy Buds 2 Pro',
                'sku' => 'HPH-SGBUDS2P',
                'category' => ProductCategory::Headphone,
                'model' => 'Galaxy Buds 2 Pro',
                'color' => 'Grafite',
                'condition' => ProductCondition::New,
                'cost_price' => 650.00,
                'sale_price' => 999.00,
                'stock_quantity' => 15,
                'min_stock_alert' => 4,
            ],
        ];

        foreach ($headphones as $data) {
            Product::create($data);
        }
        $this->command->info('4 fones de ouvido criados.');

        // ========================================
        // ELETRÔNICOS - CAIXAS DE SOM
        // ========================================
        $speakers = [
            [
                'name' => 'HomePod Mini',
                'sku' => 'SPK-HOMEMINI',
                'category' => ProductCategory::Speaker,
                'model' => 'HomePod Mini',
                'color' => 'Cinza Espacial',
                'condition' => ProductCondition::New,
                'cost_price' => 550.00,
                'sale_price' => 899.00,
                'stock_quantity' => 10,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'JBL Charge 5',
                'sku' => 'SPK-JBLCHARGE5',
                'category' => ProductCategory::Speaker,
                'model' => 'Charge 5',
                'color' => 'Azul',
                'condition' => ProductCondition::New,
                'cost_price' => 650.00,
                'sale_price' => 999.00,
                'stock_quantity' => 8,
                'min_stock_alert' => 2,
            ],
        ];

        foreach ($speakers as $data) {
            Product::create($data);
        }
        $this->command->info('2 caixas de som criadas.');

        // ========================================
        // PERFUMES
        // ========================================
        $perfumes = [
            [
                'name' => 'Sauvage Dior EDT 100ml',
                'sku' => 'PRF-SAUVAGE100',
                'category' => ProductCategory::Perfume,
                'model' => 'Sauvage',
                'color' => 'EDT 100ml',
                'condition' => ProductCondition::New,
                'cost_price' => 350.00,
                'sale_price' => 599.00,
                'stock_quantity' => 15,
                'min_stock_alert' => 5,
            ],
            [
                'name' => 'Bleu de Chanel EDP 100ml',
                'sku' => 'PRF-BLEUCHANEL',
                'category' => ProductCategory::Perfume,
                'model' => 'Bleu de Chanel',
                'color' => 'EDP 100ml',
                'condition' => ProductCondition::New,
                'cost_price' => 420.00,
                'sale_price' => 699.00,
                'stock_quantity' => 12,
                'min_stock_alert' => 4,
            ],
            [
                'name' => 'Acqua di Gio Profumo 125ml',
                'sku' => 'PRF-ADGPROF',
                'category' => ProductCategory::Perfume,
                'model' => 'Acqua di Gio',
                'color' => 'Profumo 125ml',
                'condition' => ProductCondition::New,
                'cost_price' => 380.00,
                'sale_price' => 649.00,
                'stock_quantity' => 10,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'Chanel Coco Mademoiselle 100ml',
                'sku' => 'PRF-COCOMAD',
                'category' => ProductCategory::Perfume,
                'model' => 'Coco Mademoiselle',
                'color' => 'EDP 100ml',
                'condition' => ProductCondition::New,
                'cost_price' => 450.00,
                'sale_price' => 749.00,
                'stock_quantity' => 8,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'Good Girl Carolina Herrera 80ml',
                'sku' => 'PRF-GOODGIRL',
                'category' => ProductCategory::Perfume,
                'model' => 'Good Girl',
                'color' => 'EDP 80ml',
                'condition' => ProductCondition::New,
                'cost_price' => 320.00,
                'sale_price' => 549.00,
                'stock_quantity' => 10,
                'min_stock_alert' => 4,
            ],
            [
                'name' => '212 VIP Men 100ml',
                'sku' => 'PRF-212VIP',
                'category' => ProductCategory::Perfume,
                'model' => '212 VIP Men',
                'color' => 'EDT 100ml',
                'condition' => ProductCondition::New,
                'cost_price' => 280.00,
                'sale_price' => 479.00,
                'stock_quantity' => 12,
                'min_stock_alert' => 4,
            ],
            [
                'name' => 'La Vie Est Belle Lancôme 75ml',
                'sku' => 'PRF-LAVIE',
                'category' => ProductCategory::Perfume,
                'model' => 'La Vie Est Belle',
                'color' => 'EDP 75ml',
                'condition' => ProductCondition::New,
                'cost_price' => 350.00,
                'sale_price' => 589.00,
                'stock_quantity' => 9,
                'min_stock_alert' => 3,
            ],
        ];

        foreach ($perfumes as $data) {
            Product::create($data);
        }
        $this->command->info('7 perfumes criados.');

        // ========================================
        // ACESSÓRIOS
        // ========================================
        $accessories = [
            [
                'name' => 'Carregador USB-C 20W Apple',
                'sku' => 'ACC-USBC20W',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 120.00,
                'sale_price' => 199.00,
                'stock_quantity' => 25,
                'min_stock_alert' => 5,
            ],
            [
                'name' => 'Cabo USB-C para Lightning 1m',
                'sku' => 'ACC-CABLELT1M',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 80.00,
                'sale_price' => 149.00,
                'stock_quantity' => 30,
                'min_stock_alert' => 10,
            ],
            [
                'name' => 'Capa Silicone iPhone 15 Pro Max',
                'sku' => 'ACC-CAPA15PM',
                'category' => ProductCategory::Accessory,
                'model' => 'iPhone 15 Pro Max',
                'condition' => ProductCondition::New,
                'cost_price' => 80.00,
                'sale_price' => 149.00,
                'stock_quantity' => 20,
                'min_stock_alert' => 5,
            ],
            [
                'name' => 'Película de Vidro iPhone 15',
                'sku' => 'ACC-PELVD15',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 15.00,
                'sale_price' => 49.90,
                'stock_quantity' => 50,
                'min_stock_alert' => 15,
            ],
            [
                'name' => 'MagSafe Charger Apple',
                'sku' => 'ACC-MAGSAFE',
                'category' => ProductCategory::Accessory,
                'condition' => ProductCondition::New,
                'cost_price' => 200.00,
                'sale_price' => 399.00,
                'stock_quantity' => 12,
                'min_stock_alert' => 3,
            ],
            [
                'name' => 'Capa Samsung Galaxy S24 Ultra',
                'sku' => 'ACC-CAPAS24U',
                'category' => ProductCategory::Accessory,
                'model' => 'Galaxy S24 Ultra',
                'condition' => ProductCondition::New,
                'cost_price' => 50.00,
                'sale_price' => 99.00,
                'stock_quantity' => 25,
                'min_stock_alert' => 5,
            ],
        ];

        foreach ($accessories as $data) {
            Product::create($data);
        }
        $this->command->info('6 acessórios criados.');

        // ========================================
        // SERVIÇOS
        // ========================================
        $services = [
            [
                'name' => 'Troca de Tela iPhone',
                'sku' => 'SRV-TELAIPH',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 400.00,
                'sale_price' => 800.00,
                'stock_quantity' => 999,
                'min_stock_alert' => 0,
            ],
            [
                'name' => 'Troca de Bateria Smartphone',
                'sku' => 'SRV-BATERIA',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 100.00,
                'sale_price' => 250.00,
                'stock_quantity' => 999,
                'min_stock_alert' => 0,
            ],
            [
                'name' => 'Backup e Transferência de Dados',
                'sku' => 'SRV-BACKUP',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 0.00,
                'sale_price' => 80.00,
                'stock_quantity' => 999,
                'min_stock_alert' => 0,
            ],
            [
                'name' => 'Formatação e Configuração',
                'sku' => 'SRV-FORMAT',
                'category' => ProductCategory::Service,
                'condition' => ProductCondition::New,
                'cost_price' => 0.00,
                'sale_price' => 100.00,
                'stock_quantity' => 999,
                'min_stock_alert' => 0,
            ],
        ];

        foreach ($services as $data) {
            Product::create($data);
        }
        $this->command->info('4 serviços criados.');

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('Seeding concluído com sucesso!');
        $this->command->info('Total de produtos: 38');
        $this->command->info('===========================================');
    }
}
