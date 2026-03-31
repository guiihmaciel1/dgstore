<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('battery_health')->nullable()->after('condition');
            $table->boolean('has_box')->default(false)->after('battery_health');
            $table->boolean('has_cable')->default(false)->after('has_box');
        });
    }

    public function down(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->dropColumn(['battery_health', 'has_box', 'has_cable']);
        });
    }
};
