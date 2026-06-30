<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negotiation_snapshots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('product_description');
            $table->decimal('product_price', 10, 2);
            $table->decimal('product_cost', 10, 2)->nullable();
            $table->string('trade_in_model')->nullable();
            $table->decimal('trade_in_value', 10, 2)->nullable();
            $table->decimal('trade_in_system_value', 10, 2)->nullable();
            $table->decimal('down_payment', 10, 2)->default(0);
            $table->decimal('card_balance', 10, 2)->default(0);
            $table->decimal('commission_estimate', 10, 2)->default(0);
            $table->text('message_text')->nullable();
            $table->text('notes')->nullable();
            $table->date('expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'converted'])->default('active');
            $table->foreignUlid('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiation_snapshots');
    }
};
