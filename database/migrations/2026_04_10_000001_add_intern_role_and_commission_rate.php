<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role
            ENUM('admin_geral','admin_b2b','admin_perfumes','seller','seller_b2b','intern') DEFAULT 'seller'");

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('active');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role
            ENUM('admin_geral','admin_b2b','admin_perfumes','seller','seller_b2b') DEFAULT 'seller'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
