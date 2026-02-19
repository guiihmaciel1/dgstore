<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de clientes (consumidor final)
        Schema::create('perfume_customers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('phone');
            $table->string('cpf')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela de encomendas (reservas com sinal)
        Schema::create('perfume_reservations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reservation_number')->unique();
            $table->foreignUlid('perfume_customer_id')->constrained('perfume_customers')->cascadeOnDelete();
            $table->foreignUlid('perfume_product_id')->nullable()->constrained('perfume_products')->nullOnDelete();
            $table->text('product_description')->nullable();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('product_price', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('deposit_paid', 10, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled', 'expired'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->ulid('converted_perfume_sale_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabela de pagamentos de encomendas
        Schema::create('perfume_reservation_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('perfume_reservation_id')->constrained('perfume_reservations')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['pix', 'cash', 'card'])->default('pix');
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabela de vendas B2C
        Schema::create('perfume_sales', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('sale_number')->unique();
            $table->foreignUlid('perfume_customer_id')->constrained('perfume_customers')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'pix', 'mixed'])->default('cash');
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->integer('installments')->default(1);
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'cancelled'])->default('paid');
            $table->timestamp('sold_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela de itens das vendas B2C
        Schema::create('perfume_sale_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('perfume_sale_id')->constrained('perfume_sales')->cascadeOnDelete();
            $table->foreignUlid('perfume_product_id')->constrained('perfume_products')->cascadeOnDelete();
            $table->json('product_snapshot')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        // Adicionar FK da encomenda convertida após criar perfume_sales
        Schema::table('perfume_reservations', function (Blueprint $table) {
            $table->foreign('converted_perfume_sale_id')
                ->references('id')->on('perfume_sales')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfume_sale_items');
        Schema::dropIfExists('perfume_sales');
        Schema::dropIfExists('perfume_reservation_payments');
        Schema::dropIfExists('perfume_reservations');
        Schema::dropIfExists('perfume_customers');
    }
};
