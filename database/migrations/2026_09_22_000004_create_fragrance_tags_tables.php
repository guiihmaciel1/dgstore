<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fragrance_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#ec4899');
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('fragrance_product_tag', function (Blueprint $table) {
            $table->foreignUlid('fragrance_product_id')
                ->constrained('fragrance_products')
                ->cascadeOnDelete();
            $table->foreignId('fragrance_tag_id')
                ->constrained('fragrance_tags')
                ->cascadeOnDelete();
            $table->primary(['fragrance_product_id', 'fragrance_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrance_product_tag');
        Schema::dropIfExists('fragrance_tags');
    }
};
