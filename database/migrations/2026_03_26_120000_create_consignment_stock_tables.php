<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_stock_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->string('imei')->nullable()->unique();
            $table->decimal('supplier_cost', 10, 2);
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->enum('status', ['available', 'sold', 'returned'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamp('sold_at')->nullable();
            $table->foreignUlid('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('consignment_stock_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('consignment_item_id')->constrained('consignment_stock_items')->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['in', 'out', 'return']);
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->ulid('reference_id')->nullable();
            $table->timestamp('created_at');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignUlid('consignment_item_id')->nullable()->after('subtotal')
                ->constrained('consignment_stock_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consignment_item_id');
        });

        Schema::dropIfExists('consignment_stock_movements');
        Schema::dropIfExists('consignment_stock_items');
    }
};
