<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_clock_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['clock_in', 'lunch_out', 'lunch_in', 'clock_out']);
            $table->timestamp('punched_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'punched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_clock_entries');
    }
};
