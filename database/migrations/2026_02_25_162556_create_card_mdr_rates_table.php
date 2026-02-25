<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabela de taxas MDR (Merchant Discount Rate) para cálculo de tarifas Stone
     */
    public function up(): void
    {
        Schema::create('card_mdr_rates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('payment_type', ['debit', 'credit'])->comment('Tipo de pagamento');
            $table->unsignedTinyInteger('installments')->comment('Número de parcelas (1-18)');
            $table->decimal('mdr_rate', 5, 4)->comment('Taxa MDR em percentual (ex: 9.9900)');
            $table->boolean('is_active')->default(true)->comment('Taxa ativa');
            $table->timestamps();

            // Índice único para garantir uma taxa ativa por tipo/parcela
            $table->unique(['payment_type', 'installments', 'is_active'], 'unique_active_rate');
            
            // Índices para consultas rápidas
            $table->index('payment_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_mdr_rates');
    }
};
