<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrige a precisão da coluna mdr_rate de decimal(5,4) para decimal(6,4)
     * para suportar taxas até 99.9999% (ex: 16.35%)
     */
    public function up(): void
    {
        Schema::table('card_mdr_rates', function (Blueprint $table) {
            $table->decimal('mdr_rate', 6, 4)->change()->comment('Taxa MDR em percentual (ex: 16.3500)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_mdr_rates', function (Blueprint $table) {
            $table->decimal('mdr_rate', 5, 4)->change()->comment('Taxa MDR em percentual (ex: 9.9900)');
        });
    }
};
