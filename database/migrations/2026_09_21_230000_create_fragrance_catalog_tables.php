<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fragrance_products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('fragrantica_id')->unique();
            $table->string('fragrantica_url');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('slug')->unique();
            $table->enum('gender', ['masculino', 'feminino', 'unissex'])->default('unissex');
            $table->text('description')->nullable();
            $table->string('concentration')->nullable();
            $table->smallInteger('year')->unsigned()->nullable();
            $table->string('size_ml')->nullable();
            $table->string('image_url')->nullable();
            $table->string('brand_logo_url')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('votes_count')->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->json('seasons')->nullable();
            $table->json('day_night')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('brand');
            $table->index('gender');
            $table->index('active');
        });

        Schema::create('fragrance_accords', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('fragrance_product_id')
                ->constrained('fragrance_products')
                ->cascadeOnDelete();
            $table->string('name');
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('color')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('fragrance_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('fragrance_product_id')
                ->constrained('fragrance_products')
                ->cascadeOnDelete();
            $table->enum('layer', ['top', 'heart', 'base']);
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrance_notes');
        Schema::dropIfExists('fragrance_accords');
        Schema::dropIfExists('fragrance_products');
    }
};
