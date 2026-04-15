<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_item_images', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sale_item_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('sale_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_item_images');
    }
};
