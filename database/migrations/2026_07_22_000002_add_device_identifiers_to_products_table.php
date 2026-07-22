<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('imei2', 20)->nullable()->after('imei');
            $table->string('serial_number', 30)->nullable()->after('imei2');
            $table->string('model_number', 20)->nullable()->after('serial_number');
            $table->string('part_number', 30)->nullable()->after('model_number');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['imei2', 'serial_number', 'model_number', 'part_number']);
        });
    }
};
