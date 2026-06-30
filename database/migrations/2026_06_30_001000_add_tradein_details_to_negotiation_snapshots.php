<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negotiation_snapshots', function (Blueprint $table) {
            $table->string('trade_in_storage')->nullable()->after('trade_in_model');
            $table->unsignedTinyInteger('trade_in_battery')->nullable()->after('trade_in_storage');
        });
    }

    public function down(): void
    {
        Schema::table('negotiation_snapshots', function (Blueprint $table) {
            $table->dropColumn(['trade_in_storage', 'trade_in_battery']);
        });
    }
};
