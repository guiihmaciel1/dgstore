<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('request_number', 20)->unique();

            // Cliente (obrigatório)
            $table->ulid('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            // Vendedora que solicitou
            $table->ulid('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->string('seller_name', 100);

            // Tipo da solicitação
            $table->string('type', 20); // direct_purchase, upgrade

            // Produto desejado (descrição - não está no estoque ainda)
            $table->string('desired_product'); // "iPhone 15 Pro Max 256GB Titânio Natural"
            $table->string('desired_model')->nullable();
            $table->string('desired_storage', 20)->nullable();
            $table->string('desired_color', 50)->nullable();
            $table->string('desired_condition', 10)->default('new');
            $table->decimal('estimated_cost', 10, 2)->nullable();

            // Dados da venda
            $table->decimal('sale_price', 10, 2);
            $table->decimal('down_payment', 10, 2);
            $table->string('down_payment_method', 10);
            $table->string('payment_method', 20);
            $table->unsignedTinyInteger('installments')->nullable();

            // Upgrade (quando type = upgrade)
            $table->string('trade_in_device')->nullable();
            $table->decimal('trade_in_value', 10, 2)->nullable();
            $table->decimal('upgrade_difference', 10, 2)->nullable();

            // Entrega
            $table->string('delivery_type', 10)->default('pickup');
            $table->string('delivery_address')->nullable();
            $table->string('delivery_time', 50)->nullable();
            $table->text('delivery_notes')->nullable();

            // Status e workflow
            $table->string('status', 20)->default('pending');
            $table->ulid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->ulid('converted_sale_id')->nullable();
            $table->foreign('converted_sale_id')->references('id')->on('sales')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('seller_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
