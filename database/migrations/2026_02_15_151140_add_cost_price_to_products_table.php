<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->after('imei');
        });

        // Preenche cost_price para produtos já criados via trade-in
        DB::table('trade_ins')
            ->where('status', 'processed')
            ->whereNotNull('product_id')
            ->orderBy('id')
            ->chunk(100, function ($tradeIns) {
                foreach ($tradeIns as $tradeIn) {
                    DB::table('products')
                        ->where('id', $tradeIn->product_id)
                        ->whereNull('cost_price')
                        ->update(['cost_price' => $tradeIn->estimated_value]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
