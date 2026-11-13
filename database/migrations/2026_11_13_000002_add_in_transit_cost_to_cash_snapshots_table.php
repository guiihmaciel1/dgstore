<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda na conferência de caixa o valor das compras pagas e ainda não recebidas.
 * Apenas adiciona uma coluna com padrão 0; não altera dados existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_snapshots', function (Blueprint $table) {
            $table->decimal('in_transit_cost', 14, 2)->default(0)->after('stock_other_cost');
        });
    }

    public function down(): void
    {
        Schema::table('cash_snapshots', function (Blueprint $table) {
            $table->dropColumn('in_transit_cost');
        });
    }
};
