<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_prices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('active');
            $table->index('sort_order');
        });

        Schema::create('marketing_creatives', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('active')->default(true);
            $table->date('date');
            $table->timestamps();

            $table->index(['date', 'active']);
        });

        Schema::create('marketing_used_listings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('trade_in_price', 10, 2)->nullable();
            $table->decimal('resale_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->boolean('has_box')->default(false);
            $table->boolean('has_cable')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_used_listings');
        Schema::dropIfExists('marketing_creatives');
        Schema::dropIfExists('marketing_prices');
    }
};
