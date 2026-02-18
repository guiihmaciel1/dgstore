<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfume_retailers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('document')->nullable();
            $table->string('whatsapp');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('perfume_products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->enum('category', ['masculino', 'feminino', 'unissex'])->default('unissex');
            $table->string('size_ml')->nullable();
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('photo')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('perfume_samples', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('perfume_product_id')->constrained('perfume_products')->cascadeOnDelete();
            $table->foreignUlid('perfume_retailer_id')->constrained('perfume_retailers')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->enum('status', ['delivered', 'with_retailer', 'returned'])->default('delivered');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('perfume_orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUlid('perfume_retailer_id')->constrained('perfume_retailers')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['received', 'separating', 'shipped', 'delivered', 'cancelled'])->default('received');
            $table->enum('payment_method', ['pix', 'consignment'])->default('pix');
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('perfume_order_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('perfume_order_id')->constrained('perfume_orders')->cascadeOnDelete();
            $table->foreignUlid('perfume_product_id')->constrained('perfume_products')->cascadeOnDelete();
            $table->json('product_snapshot')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('perfume_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('perfume_order_id')->constrained('perfume_orders')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['pix', 'cash', 'transfer'])->default('pix');
            $table->string('reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('perfume_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfume_payments');
        Schema::dropIfExists('perfume_order_items');
        Schema::dropIfExists('perfume_orders');
        Schema::dropIfExists('perfume_samples');
        Schema::dropIfExists('perfume_products');
        Schema::dropIfExists('perfume_retailers');
        Schema::dropIfExists('perfume_settings');
    }
};
