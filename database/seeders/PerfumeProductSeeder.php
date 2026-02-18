<?php

namespace Database\Seeders;

use App\Domain\Perfumes\Models\PerfumeProduct;
use Illuminate\Database\Seeder;

class PerfumeProductSeeder extends Seeder
{
    public function run(): void
    {
        PerfumeProduct::query()->forceDelete();

        $products = [
            // Masculinos
            ['name' => 'SAUVAGE EDP', 'brand' => 'DIOR', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 45.00, 'barcode' => '3348901368254'],
            ['name' => 'BLEU DE CHANEL EDP', 'brand' => 'CHANEL', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 50.00, 'barcode' => '3145891073607'],
            ['name' => 'ACQUA DI GIO PROFONDO', 'brand' => 'GIORGIO ARMANI', 'category' => 'masculino', 'size_ml' => '125', 'sale_price' => 42.00, 'barcode' => '3614272865228'],
            ['name' => 'DYLAN BLUE POUR HOMME EDT', 'brand' => 'VERSACE', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 28.00, 'barcode' => '8011003825745'],
            ['name' => 'EROS EDT', 'brand' => 'VERSACE', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 30.00, 'barcode' => '8011003809219'],
            ['name' => '1 MILLION EDT', 'brand' => 'PACO RABANNE', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 32.00, 'barcode' => '3349668508457'],
            ['name' => 'INVICTUS EDT', 'brand' => 'PACO RABANNE', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 30.00, 'barcode' => '3349668515660'],
            ['name' => 'THE ONE EDP', 'brand' => 'DOLCE & GABBANA', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 35.00, 'barcode' => '3423473021360'],
            ['name' => 'COOL WATER EDT', 'brand' => 'DAVIDOFF', 'category' => 'masculino', 'size_ml' => '125', 'sale_price' => 18.00, 'barcode' => '3414202000572'],
            ['name' => 'LIBRE HOMME EDT', 'brand' => 'YVES SAINT LAURENT', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 38.00, 'barcode' => '3614273903103'],
            ['name' => 'AMEER AL OUDH INTENSE SPRAY', 'brand' => 'LATTAFA', 'category' => 'masculino', 'size_ml' => '200', 'sale_price' => 2.00, 'barcode' => '6290360591414'],
            ['name' => 'ASAD EDP', 'brand' => 'LATTAFA', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 8.00, 'barcode' => '6290360594538'],
            ['name' => 'QAED AL FURSAN EDP', 'brand' => 'LATTAFA', 'category' => 'masculino', 'size_ml' => '90', 'sale_price' => 7.00, 'barcode' => '6291108734124'],
            ['name' => 'BLUE SEDUCTION EDT', 'brand' => 'ANTONIO BANDERAS', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 12.00, 'barcode' => '8411061016729'],
            ['name' => 'KING OF SEDUCTION EDT', 'brand' => 'ANTONIO BANDERAS', 'category' => 'masculino', 'size_ml' => '100', 'sale_price' => 12.00, 'barcode' => '8411061070710'],

            // Femininos
            ['name' => 'COCO MADEMOISELLE EDP', 'brand' => 'CHANEL', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 55.00, 'barcode' => '3145891165203'],
            ['name' => "J'ADORE EDP", 'brand' => 'DIOR', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 48.00, 'barcode' => '3348901237115'],
            ['name' => 'LA VIE EST BELLE EDP', 'brand' => 'LANCOME', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 42.00, 'barcode' => '3605532612768'],
            ['name' => 'GOOD GIRL EDP', 'brand' => 'CAROLINA HERRERA', 'category' => 'feminino', 'size_ml' => '80', 'sale_price' => 40.00, 'barcode' => '8411061860434'],
            ['name' => 'BLACK OPIUM EDP', 'brand' => 'YVES SAINT LAURENT', 'category' => 'feminino', 'size_ml' => '90', 'sale_price' => 40.00, 'barcode' => '3365440787971'],
            ['name' => 'LIBRE EDP', 'brand' => 'YVES SAINT LAURENT', 'category' => 'feminino', 'size_ml' => '90', 'sale_price' => 42.00, 'barcode' => '3614272648425'],
            ['name' => 'YARA EDP', 'brand' => 'LATTAFA', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 8.00, 'barcode' => '6291108734940'],
            ['name' => 'BADE\'E AL OUD AMETHYST EDP', 'brand' => 'LATTAFA', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 9.00, 'barcode' => '6291108735053'],
            ['name' => '100% PASSION WOMEN EDT', 'brand' => 'ABERCROMBIE & FITCH', 'category' => 'feminino', 'size_ml' => '100', 'sale_price' => 55.00, 'barcode' => '085715169990'],
            ['name' => 'HER MAJESTY EDP', 'brand' => 'AURORA SCENT', 'category' => 'feminino', 'size_ml' => '75', 'sale_price' => 37.00, 'barcode' => '6290360541648'],
            ['name' => 'SECRET HER ABSOLUT EDP', 'brand' => 'ANTONIO BANDERAS', 'category' => 'feminino', 'size_ml' => '80', 'sale_price' => 18.50, 'barcode' => '8411061088876'],

            // Unissex
            ['name' => 'CK ONE EDT', 'brand' => 'CALVIN KLEIN', 'category' => 'unissex', 'size_ml' => '200', 'sale_price' => 22.00, 'barcode' => '088300107407'],
            ['name' => 'CK BE EDT', 'brand' => 'CALVIN KLEIN', 'category' => 'unissex', 'size_ml' => '200', 'sale_price' => 20.00, 'barcode' => '088300104437'],
            ['name' => 'AUTUMN DECIDUOUS EDP', 'brand' => 'AURORA SCENT', 'category' => 'unissex', 'size_ml' => '100', 'sale_price' => 34.00, 'barcode' => '6290360540269'],
            ['name' => 'SPRING DECIDUOUS EDP', 'brand' => 'AURORA SCENT', 'category' => 'unissex', 'size_ml' => '100', 'sale_price' => 34.00, 'barcode' => '6290360540276'],
            ['name' => 'SUMMER DECIDUOUS EDP', 'brand' => 'AURORA SCENT', 'category' => 'unissex', 'size_ml' => '100', 'sale_price' => 34.00, 'barcode' => '6290360540283'],
        ];

        foreach ($products as $data) {
            PerfumeProduct::create(array_merge($data, [
                'cost_price' => 0,
                'stock_quantity' => 0,
                'active' => true,
            ]));
        }
    }
}
