<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cria tabelas para: Garantias, Pedidos de Importação e Reservas
     */
    public function up(): void
    {
        // Adiciona campo reserved em products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('reserved')->default(false)->after('active');
            $table->ulid('reserved_by')->nullable()->after('reserved');
            $table->index('reserved');
        });

        // =============================================
        // MÓDULO DE GARANTIAS
        // =============================================

        // Tabela de garantias (vinculada ao item vendido)
        Schema::create('warranties', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sale_item_id')->constrained('sale_items')->cascadeOnDelete();
            $table->integer('supplier_warranty_months')->default(0);
            $table->integer('customer_warranty_months')->default(0);
            $table->date('supplier_warranty_until')->nullable();
            $table->date('customer_warranty_until')->nullable();
            $table->string('imei')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_warranty_until');
            $table->index('customer_warranty_until');
            $table->index('imei');
        });

        // Tabela de acionamentos de garantia
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('warranty_id')->constrained('warranties')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['supplier', 'customer']);
            $table->enum('status', ['opened', 'in_progress', 'resolved', 'denied'])->default('opened');
            $table->text('reason');
            $table->text('resolution')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('opened_at');
        });

        // =============================================
        // MÓDULO DE PEDIDOS DE IMPORTAÇÃO
        // =============================================

        // Tabela de pedidos de importação
        Schema::create('import_orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUlid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['ordered', 'shipped', 'in_transit', 'customs', 'received', 'cancelled'])->default('ordered');
            $table->string('tracking_code')->nullable();
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->decimal('exchange_rate', 8, 4)->default(5.00);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->date('ordered_at');
            $table->date('shipped_at')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->date('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('order_number');
            $table->index('status');
            $table->index('ordered_at');
        });

        // Tabela de itens do pedido de importação
        Schema::create('import_order_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('import_order_id')->constrained('import_orders')->cascadeOnDelete();
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2);
            $table->integer('received_quantity')->default(0);
            $table->timestamps();

            $table->index(['import_order_id', 'product_id']);
        });

        // =============================================
        // MÓDULO DE RESERVAS
        // =============================================

        // Tabela de reservas
        Schema::create('reservations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reservation_number')->unique();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUlid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['active', 'converted', 'cancelled', 'expired'])->default('active');
            $table->decimal('product_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('deposit_paid', 10, 2)->default(0);
            $table->date('expires_at');
            $table->foreignUlid('converted_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('reservation_number');
            $table->index('status');
            $table->index('expires_at');
        });

        // Tabela de pagamentos de sinal
        Schema::create('reservation_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'pix', 'bank_transfer']);
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('import_order_items');
        Schema::dropIfExists('import_orders');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranties');

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['reserved']);
            $table->dropColumn(['reserved', 'reserved_by']);
        });
    }
};
