<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2b_retailers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('store_name');
            $table->string('owner_name');
            $table->string('document', 18)->unique();
            $table->string('whatsapp', 20);
            $table->string('city');
            $table->string('state', 2);
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['pending', 'approved', 'blocked'])->default('pending');
            $table->rememberToken();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('b2b_products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->enum('condition', ['sealed', 'semi_new'])->default('sealed');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('wholesale_price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('photo')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('model');
            $table->index('condition');
            $table->index('active');
            $table->index('sort_order');
        });

        Schema::create('b2b_orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUlid('b2b_retailer_id')->constrained('b2b_retailers')->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['received', 'separating', 'shipped', 'completed', 'cancelled'])->default('received');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('order_number');
            $table->index('created_at');
        });

        Schema::create('b2b_order_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('b2b_order_id')->constrained('b2b_orders')->cascadeOnDelete();
            $table->foreignUlid('b2b_product_id')->constrained('b2b_products')->cascadeOnDelete();
            $table->json('product_snapshot');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('cost_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->index(['b2b_order_id', 'b2b_product_id']);
        });

        Schema::create('b2b_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_order_items');
        Schema::dropIfExists('b2b_orders');
        Schema::dropIfExists('b2b_products');
        Schema::dropIfExists('b2b_retailers');
        Schema::dropIfExists('b2b_settings');
    }
};
