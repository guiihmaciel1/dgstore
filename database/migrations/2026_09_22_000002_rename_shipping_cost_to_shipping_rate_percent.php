<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->renameColumn('shipping_cost', 'shipping_rate_percent');
        });
    }

    public function down(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->renameColumn('shipping_rate_percent', 'shipping_cost');
        });
    }
};
