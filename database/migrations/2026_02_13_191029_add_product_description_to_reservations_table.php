<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'product_description')) {
                $table->string('product_description')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('reservations', 'source')) {
                $table->string('source')->default('stock')->after(
                    Schema::hasColumn('reservations', 'product_description') ? 'product_description' : 'product_id'
                );
            }
        });

        // Tornar product_id nullable via SQL direto (mais seguro com FK)
        try {
            DB::statement('ALTER TABLE reservations MODIFY product_id CHAR(26) NULL');
        } catch (\Throwable $e) {
            // Se falhar, ignora — pode já ser nullable ou ter constraint
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'product_description')) {
                $table->dropColumn('product_description');
            }
            if (Schema::hasColumn('reservations', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
