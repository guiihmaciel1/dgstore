<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_interests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignUlid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('model');
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->string('condition')->nullable();
            $table->decimal('max_budget', 12, 2)->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->index(['notified', 'model']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_interests');
    }
};
