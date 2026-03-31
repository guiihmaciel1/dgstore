<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_batches', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('batch_code')->unique();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->enum('condition', ['new', 'used'])->default('new');
            $table->decimal('supplier_cost', 10, 2);
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->integer('total_quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
        });

        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->foreignUlid('batch_id')->nullable()->after('supplier_id')
                ->constrained('consignment_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
        });

        Schema::dropIfExists('consignment_batches');
    }
};
