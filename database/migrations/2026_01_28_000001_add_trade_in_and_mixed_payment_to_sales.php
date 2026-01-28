<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona suporte a trade-in (aparelho como entrada) e pagamento misto
     */
    public function up(): void
    {
        // Adiciona campos de pagamento misto na tabela sales
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('trade_in_value', 10, 2)->default(0)->after('discount');
            $table->decimal('cash_payment', 10, 2)->default(0)->after('trade_in_value');
            $table->decimal('card_payment', 10, 2)->default(0)->after('cash_payment');
            $table->string('cash_payment_method')->nullable()->after('card_payment');
        });

        // Cria tabela de trade-ins (aparelhos recebidos como entrada)
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->string('device_name');
            $table->string('device_model')->nullable();
            $table->string('imei')->nullable();
            $table->decimal('estimated_value', 10, 2);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processed', 'rejected'])->default('pending');
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('device_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_ins');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'trade_in_value',
                'cash_payment',
                'card_payment',
                'cash_payment_method',
            ]);
        });
    }
};
