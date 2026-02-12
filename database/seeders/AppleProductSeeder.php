<?php

namespace Database\Seeders;

use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder do catálogo completo Apple.
 *
 * Preços de referência (custo/venda) em BRL para produtos novos.
 * Ajuste conforme necessidade. Stock inicia em 0 - adicione via movimentações.
 *
 * Uso: php artisan db:seed --class=AppleProductSeeder
 */
class AppleProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = array_merge(
            $this->fromLineup($this->iphoneLineup(), 'smartphone'),
            $this->fromLineup($this->ipadLineup(), 'tablet'),
            $this->fromLineup($this->macLineup(), 'notebook'),
            $this->singleProducts(),
        );

        $count = 0;
        foreach ($products as $data) {
            Product::updateOrCreate(['sku' => $data['sku']], $data);
            $count++;
        }

        $this->command->info("{$count} produtos Apple cadastrados com sucesso.");
    }

    // ═══════════════════════════════════════════════════════
    //  Helpers de construção
    // ═══════════════════════════════════════════════════════

    private function fromLineup(array $lineup, string $category): array
    {
        $items = [];

        foreach ($lineup as [$name, $code, $storages]) {
            $model = Str::slug($name);

            foreach ($storages as $storage => [$cost, $sale]) {
                $sLabel = str_replace(['GB', 'TB'], ['', 'T'], $storage);
                $items[] = $this->makeProduct(
                    "{$name} {$storage}", "APL-{$code}-{$sLabel}",
                    $category, $model, $storage, $cost, $sale,
                );
            }
        }

        return $items;
    }

    private function makeProduct(
        string $name, string $sku, string $category,
        string $model, ?string $storage, float $cost, float $sale,
    ): array {
        return [
            'name'            => $name,
            'sku'             => $sku,
            'category'        => $category,
            'model'           => $model,
            'storage'         => $storage,
            'condition'       => 'new',
            'cost_price'      => $cost,
            'sale_price'      => $sale,
            'stock_quantity'  => 0,
            'min_stock_alert' => 1,
            'supplier'        => 'Apple',
            'active'          => true,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  iPhones — [Nome, CódigoSKU, [Armazenamento => [Custo, Venda]]]
    // ═══════════════════════════════════════════════════════

    private function iphoneLineup(): array
    {
        return [
            // ── iPhone 16 ──
            ['iPhone 16 Pro Max', 'I16PM', ['256GB'=>[8500,10499], '512GB'=>[9800,12499], '1TB'=>[11200,14499]]],
            ['iPhone 16 Pro',     'I16PR', ['128GB'=>[7000,8999], '256GB'=>[7800,9999], '512GB'=>[9000,11499], '1TB'=>[10200,13499]]],
            ['iPhone 16 Plus',    'I16PL', ['128GB'=>[6200,7999], '256GB'=>[7000,8999], '512GB'=>[8000,10499]]],
            ['iPhone 16',         'I16',   ['128GB'=>[5500,6999], '256GB'=>[6200,7999], '512GB'=>[7200,9499]]],

            // ── iPhone 15 ──
            ['iPhone 15 Pro Max', 'I15PM', ['256GB'=>[7200,8999], '512GB'=>[8500,10499], '1TB'=>[9800,11999]]],
            ['iPhone 15 Pro',     'I15PR', ['128GB'=>[6000,7499], '256GB'=>[6800,8499], '512GB'=>[8000,9999], '1TB'=>[9200,11499]]],
            ['iPhone 15 Plus',    'I15PL', ['128GB'=>[5200,6499], '256GB'=>[5800,7499], '512GB'=>[6800,8499]]],
            ['iPhone 15',         'I15',   ['128GB'=>[4500,5799], '256GB'=>[5200,6499], '512GB'=>[6200,7499]]],

            // ── iPhone 14 ──
            ['iPhone 14 Pro Max', 'I14PM', ['128GB'=>[5500,6999], '256GB'=>[6200,7499], '512GB'=>[7200,8999], '1TB'=>[8500,10499]]],
            ['iPhone 14 Pro',     'I14PR', ['128GB'=>[4800,5999], '256GB'=>[5500,6999], '512GB'=>[6500,7999], '1TB'=>[7500,9499]]],
            ['iPhone 14 Plus',    'I14PL', ['128GB'=>[4200,5499], '256GB'=>[4800,5999], '512GB'=>[5800,6999]]],
            ['iPhone 14',         'I14',   ['128GB'=>[3700,4799], '256GB'=>[4200,5299], '512GB'=>[5200,6299]]],

            // ── iPhone 13 ──
            ['iPhone 13 Pro Max', 'I13PM', ['128GB'=>[4500,5699], '256GB'=>[5000,6299], '512GB'=>[6000,7299], '1TB'=>[7000,8299]]],
            ['iPhone 13 Pro',     'I13PR', ['128GB'=>[3800,4799], '256GB'=>[4300,5499], '512GB'=>[5300,6499], '1TB'=>[6300,7499]]],
            ['iPhone 13',         'I13',   ['128GB'=>[3000,3999], '256GB'=>[3500,4499], '512GB'=>[4500,5499]]],
            ['iPhone 13 Mini',    'I13MI', ['128GB'=>[2800,3499], '256GB'=>[3200,3999], '512GB'=>[4000,4999]]],

            // ── iPhone 12 ──
            ['iPhone 12 Pro Max', 'I12PM', ['128GB'=>[3200,3999], '256GB'=>[3600,4499], '512GB'=>[4200,5199]]],
            ['iPhone 12 Pro',     'I12PR', ['128GB'=>[2800,3499], '256GB'=>[3200,3999], '512GB'=>[3800,4699]]],
            ['iPhone 12',         'I12',   ['64GB'=>[2200,2999], '128GB'=>[2500,3299], '256GB'=>[3000,3799]]],
            ['iPhone 12 Mini',    'I12MI', ['64GB'=>[1800,2499], '128GB'=>[2200,2799], '256GB'=>[2600,3299]]],

            // ── iPhone SE & 11 ──
            ['iPhone SE 3a Geracao', 'ISE3', ['64GB'=>[2000,2799], '128GB'=>[2300,3099], '256GB'=>[2800,3599]]],
            ['iPhone 11 Pro Max', 'I11PM', ['64GB'=>[2500,3299], '256GB'=>[3000,3799], '512GB'=>[3500,4299]]],
            ['iPhone 11 Pro',     'I11PR', ['64GB'=>[2200,2899], '256GB'=>[2700,3499], '512GB'=>[3200,3999]]],
            ['iPhone 11',         'I11',   ['64GB'=>[1800,2499], '128GB'=>[2100,2799], '256GB'=>[2500,3299]]],
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  iPads
    // ═══════════════════════════════════════════════════════

    private function ipadLineup(): array
    {
        return [
            ['iPad Pro M4 13',       'IPDP13', ['256GB'=>[9000,11999], '512GB'=>[10500,13499], '1TB'=>[12500,15999], '2TB'=>[14500,18999]]],
            ['iPad Pro M4 11',       'IPDP11', ['256GB'=>[7500,9999], '512GB'=>[9000,11499], '1TB'=>[10500,13999], '2TB'=>[12500,16499]]],
            ['iPad Air M2 13',       'IPDA13', ['128GB'=>[6500,8499], '256GB'=>[7200,9499], '512GB'=>[8500,10999], '1TB'=>[9800,12999]]],
            ['iPad Air M2 11',       'IPDA11', ['128GB'=>[5200,6999], '256GB'=>[5800,7499], '512GB'=>[7000,8999], '1TB'=>[8200,10499]]],
            ['iPad Mini 6a Geracao', 'IPDM6',  ['64GB'=>[3500,4499], '256GB'=>[4500,5499]]],
            ['iPad 10a Geracao',     'IPD10',  ['64GB'=>[3000,3799], '256GB'=>[3800,4799]]],
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  MacBooks, iMac & Mac Mini
    // ═══════════════════════════════════════════════════════

    private function macLineup(): array
    {
        return [
            ['MacBook Pro 16 M3 Pro', 'MBP16', ['512GB'=>[15000,19999], '1TB'=>[17500,22999]]],
            ['MacBook Pro 14 M3 Pro', 'MBP14', ['512GB'=>[12500,16499], '1TB'=>[14500,18999]]],
            ['MacBook Air 15 M3',     'MBA15', ['256GB'=>[9500,12499], '512GB'=>[10800,13999]]],
            ['MacBook Air 13 M3',     'MBA13', ['256GB'=>[8000,10499], '512GB'=>[9200,11999]]],
            ['iMac 24 M3',            'IMAC24', ['256GB'=>[10000,13499], '512GB'=>[12000,15999]]],
            ['Mac Mini M3',           'MMINI', ['256GB'=>[4500,5999], '512GB'=>[5500,7499]]],
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  Apple Watch & AirPods (sem variação de armazenamento)
    // ═══════════════════════════════════════════════════════

    private function singleProducts(): array
    {
        return [
            // Apple Watch
            $this->makeProduct('Apple Watch Ultra 2 49mm',   'APL-AWU2',    'smartwatch', 'apple-watch-ultra-2',   null, 5500, 6999),
            $this->makeProduct('Apple Watch Series 10 42mm', 'APL-AW10-42', 'smartwatch', 'apple-watch-series-10', null, 3000, 3999),
            $this->makeProduct('Apple Watch Series 10 46mm', 'APL-AW10-46', 'smartwatch', 'apple-watch-series-10', null, 3300, 4299),
            $this->makeProduct('Apple Watch SE 2a 40mm',     'APL-AWSE-40', 'smartwatch', 'apple-watch-se-2',      null, 2000, 2799),
            $this->makeProduct('Apple Watch SE 2a 44mm',     'APL-AWSE-44', 'smartwatch', 'apple-watch-se-2',      null, 2200, 2999),

            // AirPods
            $this->makeProduct('AirPods Pro 2 USB-C',  'APL-APP2',  'headphone', 'airpods-pro-2', null, 1500, 1999),
            $this->makeProduct('AirPods 3a Geracao',   'APL-AP3',   'headphone', 'airpods-3',     null, 1000, 1399),
            $this->makeProduct('AirPods Max USB-C',    'APL-APMAX', 'headphone', 'airpods-max',   null, 3800, 4999),
        ];
    }
}
