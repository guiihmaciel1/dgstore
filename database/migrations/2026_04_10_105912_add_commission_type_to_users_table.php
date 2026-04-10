<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage')->after('commission_rate');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage')->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });
    }
};
