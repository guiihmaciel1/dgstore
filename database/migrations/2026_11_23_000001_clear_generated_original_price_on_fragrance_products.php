<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * original_price era sempre gerado pelo painel como preço 10x × 1,2 (não havia
 * campo para informar um preço de referência real). O catálogo exibia isso como
 * "de R$ X" e selo de % OFF. Limpa os valores gerados; o catálogo só mostra
 * desconto quando houver um preço de referência informado de verdade.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('fragrance_products')
            ->whereNotNull('original_price')
            ->update(['original_price' => null]);
    }

    public function down(): void
    {
        // Irreversível: os valores eram derivados de sale_price e não são restaurados.
    }
};
