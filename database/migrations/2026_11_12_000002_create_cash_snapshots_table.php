<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conferências do caixa real da loja: valores informados (banco e espécie)
 * e o estoque a custo calculado no momento da conferência.
 * Apenas cria uma tabela nova; não altera nem apaga dados existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_snapshots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->decimal('bank_amount', 14, 2);
            $table->decimal('cash_amount', 14, 2);
            $table->decimal('stock_new_cost', 14, 2)->default(0);
            $table->decimal('stock_used_cost', 14, 2)->default(0);
            $table->decimal('stock_other_cost', 14, 2)->default(0);
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_snapshots');
    }
};
