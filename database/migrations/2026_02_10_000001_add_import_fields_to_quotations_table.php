<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('price_usd', 10, 2)->nullable()->after('unit_price');
            $table->decimal('exchange_rate', 8, 4)->nullable()->after('price_usd');
            $table->string('category')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['price_usd', 'exchange_rate', 'category']);
        });
    }
};
