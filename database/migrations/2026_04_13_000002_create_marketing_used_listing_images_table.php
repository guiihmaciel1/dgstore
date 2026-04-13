<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_used_listing_images', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('marketing_used_listing_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('marketing_used_listing_id', 'mul_images_listing_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_used_listing_images');
    }
};
