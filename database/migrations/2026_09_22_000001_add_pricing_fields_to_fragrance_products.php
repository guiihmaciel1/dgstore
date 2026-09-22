<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->nullable()->after('votes_count');
            $table->decimal('shipping_cost', 10, 2)->nullable()->after('cost_price');
            $table->decimal('pix_price', 10, 2)->nullable()->after('sale_price');
            $table->unsignedTinyInteger('pix_discount_percent')->default(10)->after('pix_price');
        });
    }

    public function down(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'shipping_cost', 'pix_price', 'pix_discount_percent']);
        });
    }
};
