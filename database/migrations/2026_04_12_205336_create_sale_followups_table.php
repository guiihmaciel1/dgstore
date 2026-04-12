<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_followups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users');
            $table->dateTime('contacted_at');
            $table->string('method', 30)->default('whatsapp');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sale_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_followups');
    }
};
