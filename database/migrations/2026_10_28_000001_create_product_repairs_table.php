<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_repairs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUlid('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('returned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider')->nullable();
            $table->string('reason', 500)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'returned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_repairs');
    }
};
