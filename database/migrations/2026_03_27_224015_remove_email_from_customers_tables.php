<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        Schema::table('perfume_customers', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
        });

        Schema::table('perfume_customers', function (Blueprint $table) {
            $table->string('email')->nullable()->after('cpf');
        });
    }
};
