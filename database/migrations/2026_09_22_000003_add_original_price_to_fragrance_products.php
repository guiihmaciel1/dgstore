<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->decimal('original_price', 10, 2)->nullable()->after('sale_price');
        });
    }

    public function down(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->dropColumn('original_price');
        });
    }
};
