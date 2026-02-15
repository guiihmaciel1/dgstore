<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adicionar novos campos de custo e frete em sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->after('unit_price');
            $table->string('supplier_origin', 2)->nullable()->after('cost_price'); // 'br' ou 'py'
            $table->string('freight_type', 10)->nullable()->after('supplier_origin'); // 'percentage' ou 'fixed'
            $table->decimal('freight_value', 10, 2)->default(0)->after('freight_type');
            $table->decimal('freight_amount', 10, 2)->default(0)->after('freight_value');
            $table->decimal('total_cost', 10, 2)->default(0)->after('freight_amount');
        });

        // 2. Migrar cost_price do product_snapshot para o campo direto em sale_items existentes
        DB::table('sale_items')->whereNull('cost_price')->orderBy('id')->chunk(100, function ($items) {
            foreach ($items as $item) {
                $snapshot = json_decode($item->product_snapshot, true);
                $costPrice = $snapshot['cost_price'] ?? 0;
                DB::table('sale_items')->where('id', $item->id)->update([
                    'cost_price' => $costPrice,
                    'total_cost' => $costPrice,
                ]);
            }
        });

        // 3. Remover colunas de preço da tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'sale_price']);
        });
    }

    public function down(): void
    {
        // 1. Restaurar colunas de preço na tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('imei');
            $table->decimal('sale_price', 10, 2)->default(0)->after('cost_price');
        });

        // 2. Remover novos campos de sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn([
                'cost_price',
                'supplier_origin',
                'freight_type',
                'freight_value',
                'freight_amount',
                'total_cost',
            ]);
        });
    }
};
