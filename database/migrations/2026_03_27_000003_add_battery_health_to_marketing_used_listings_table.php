<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->unsignedSmallInteger('battery_health')->nullable()->after('final_price');
        });
    }

    public function down(): void
    {
        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->dropColumn('battery_health');
        });
    }
};
