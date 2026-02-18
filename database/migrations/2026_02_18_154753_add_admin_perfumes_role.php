<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin_geral','admin_b2b','seller','seller_b2b','admin_perfumes') DEFAULT 'seller'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin_geral','admin_b2b','seller','seller_b2b') DEFAULT 'seller'");
    }
};
