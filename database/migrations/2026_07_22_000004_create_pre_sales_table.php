<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_sales', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('pre_sale_number')->unique();

            // Relacionamentos
            $table->ulid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->ulid('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->string('seller_name', 100);

            // Produto (um dos dois será preenchido)
            $table->ulid('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->ulid('consignment_item_id')->nullable();
            $table->foreign('consignment_item_id')->references('id')->on('consignment_stock_items')->nullOnDelete();
            $table->json('product_snapshot');
            $table->string('product_imei', 20);

            // Valores
            $table->decimal('unit_price', 10, 2);
            $table->decimal('cost_price', 10, 2);
            $table->string('condition', 10)->default('new');

            // Sinal
            $table->decimal('down_payment', 10, 2);
            $table->string('down_payment_method', 10)->default('pix');

            // Pagamento do saldo
            $table->string('payment_method', 20);
            $table->unsignedTinyInteger('installments')->nullable();
            $table->decimal('card_gross_amount', 10, 2)->nullable();
            $table->decimal('card_net_amount', 10, 2)->nullable();
            $table->decimal('card_fee_rate', 5, 2)->nullable();

            // Trade-in
            $table->json('trade_in_device')->nullable();
            $table->decimal('trade_in_value', 10, 2)->nullable();

            // Saldo final
            $table->decimal('final_balance', 10, 2);

            // Meta
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->ulid('converted_sale_id')->nullable();
            $table->foreign('converted_sale_id')->references('id')->on('sales')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('seller_id');
            $table->index('product_imei');
        });

        // Adicionar reserved/reserved_by em consignment_stock_items
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->boolean('reserved')->default(false)->after('status');
            $table->string('reserved_by')->nullable()->after('reserved');
            $table->index('reserved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_sales');

        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->dropIndex(['reserved']);
            $table->dropColumn(['reserved', 'reserved_by']);
        });
    }
};
