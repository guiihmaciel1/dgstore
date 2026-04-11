<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_ins', function (Blueprint $table) {
            $table->string('category')->nullable()->after('device_model');
            $table->string('storage')->nullable()->after('category');
            $table->string('color')->nullable()->after('storage');
            $table->decimal('cost_price', 10, 2)->nullable()->after('estimated_value');
            $table->decimal('sale_price', 10, 2)->nullable()->after('cost_price');
            $table->decimal('resale_price', 10, 2)->nullable()->after('sale_price');
        });
    }

    public function down(): void
    {
        Schema::table('trade_ins', function (Blueprint $table) {
            $table->dropColumn(['category', 'storage', 'color', 'cost_price', 'sale_price', 'resale_price']);
        });
    }
};
