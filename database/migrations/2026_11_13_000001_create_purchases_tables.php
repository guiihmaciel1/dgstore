<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Compras para estoque: o que já foi pago e ainda não chegou.
 * Apenas cria tabelas novas; não altera nem apaga dados existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('number', 20)->unique();
            $table->foreignUlid('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name')->nullable();
            $table->date('purchased_at');
            $table->date('expected_at')->nullable();
            $table->string('status', 12)->default('pending'); // pending | partial | received | cancelled
            $table->decimal('total', 14, 2)->default(0);
            $table->foreignUlid('account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->foreignUlid('financial_transaction_id')->nullable()->constrained('financial_transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'expected_at']);
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->string('category', 30);
            $table->string('condition', 20);
            $table->string('model', 100);
            $table->string('storage', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->decimal('unit_cost', 12, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status', 12)->default('pending'); // pending | received | cancelled
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['purchase_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
