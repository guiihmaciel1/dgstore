<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_resale_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('resaleable_type');
            $table->string('resaleable_id');
            $table->decimal('resale_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('battery_health')->nullable();
            $table->date('warranty_until')->nullable();
            $table->boolean('has_box')->default(false);
            $table->boolean('has_cable')->default(false);
            $table->text('notes')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();

            $table->unique(['resaleable_type', 'resaleable_id']);
            $table->index('visible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_resale_items');
    }
};
