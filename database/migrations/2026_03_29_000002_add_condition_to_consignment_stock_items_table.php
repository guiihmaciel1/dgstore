<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->enum('condition', ['new', 'used'])->default('new')->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('consignment_stock_items', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
};
