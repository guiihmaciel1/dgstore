<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_price_history', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('batch_id')->nullable()->constrained('consignment_batches')->nullOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('old_supplier_cost', 10, 2);
            $table->decimal('new_supplier_cost', 10, 2);
            $table->decimal('old_suggested_price', 10, 2)->nullable();
            $table->decimal('new_suggested_price', 10, 2)->nullable();
            $table->text('reason');
            $table->integer('affected_items_count')->default(0);
            $table->json('affected_item_ids')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_price_history');
    }
};
