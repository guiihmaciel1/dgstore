<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->string('serial_number')->nullable()->unique()->after('imei');
        });
    }

    public function down(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->dropUnique(['serial_number']);
            $table->dropColumn('serial_number');
        });
    }
};
