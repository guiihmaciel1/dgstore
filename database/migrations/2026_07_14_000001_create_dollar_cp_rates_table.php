<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dollar_cp_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 8, 4);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index('fetched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dollar_cp_rates');
    }
};
