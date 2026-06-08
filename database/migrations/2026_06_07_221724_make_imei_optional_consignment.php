<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verifica se o índice único do IMEI existe antes de tentar removê-lo
        $indexes = DB::select("SHOW INDEX FROM consignment_stock_items WHERE Key_name = 'consignment_stock_items_imei_unique'");
        
        if (!empty($indexes)) {
            Schema::table('consignment_stock_items', function (Blueprint $table) {
                $table->dropUnique(['imei']);
            });
        }

        // Adiciona índice composto para busca por agrupamento (com prefixos para evitar tamanho excessivo)
        // Isso otimiza a busca de produtos consolidados
        DB::statement('
            CREATE INDEX idx_consignment_grouping 
            ON consignment_stock_items (supplier_id, name(50), model(50), storage(20), color(30), `condition`, status)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove o índice de agrupamento
        DB::statement('DROP INDEX IF EXISTS idx_consignment_grouping ON consignment_stock_items');

        // Restaura unique constraint do IMEI
        // ATENÇÃO: Isso pode falhar se houver IMEIs duplicados ou nulos
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->unique('imei');
        });
    }
};
