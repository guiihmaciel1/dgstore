<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Passo 1: adiciona os novos valores ao ENUM sem remover os antigos
        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin','seller','admin_geral','admin_b2b','seller_b2b') DEFAULT 'seller'");

        // Passo 2: migra registros existentes
        DB::update("UPDATE users SET role = 'admin_geral' WHERE role = 'admin'");

        // Passo 3: remove o valor legado 'admin' do ENUM
        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin_geral','admin_b2b','seller','seller_b2b') DEFAULT 'seller'");
    }

    public function down(): void
    {
        // Reverte: adiciona 'admin' de volta e migra admin_geral → admin
        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin_geral','admin_b2b','seller','seller_b2b','admin') DEFAULT 'seller'");

        DB::update("UPDATE users SET role = 'admin' WHERE role = 'admin_geral'");

        DB::statement("ALTER TABLE users MODIFY role 
            ENUM('admin','seller') DEFAULT 'seller'");
    }
};
