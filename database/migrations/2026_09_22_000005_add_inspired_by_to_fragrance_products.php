<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->string('inspired_by')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('fragrance_products', function (Blueprint $table) {
            $table->dropColumn('inspired_by');
        });
    }
};
