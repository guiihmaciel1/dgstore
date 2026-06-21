<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('seller_name', 100)->nullable()->after('user_id');
            $table->string('delivery_type', 50)->nullable()->after('notes');
            $table->string('delivery_method', 50)->nullable()->after('delivery_type');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['seller_name', 'delivery_type', 'delivery_method']);
        });
    }
};
