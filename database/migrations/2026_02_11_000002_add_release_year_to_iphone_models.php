<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iphone_models', function (Blueprint $table) {
            $table->smallInteger('release_year')->default(2024)->after('search_term');
        });

        // Popular release_year baseado no slug (ex: iphone-15-pro-max → 15 → 2023)
        $mapping = [
            'iphone-11' => 2019,
            'iphone-12' => 2020,
            'iphone-13' => 2021,
            'iphone-14' => 2022,
            'iphone-15' => 2023,
            'iphone-16' => 2024,
        ];

        foreach ($mapping as $prefix => $year) {
            DB::table('iphone_models')
                ->where('slug', 'like', "{$prefix}%")
                ->update(['release_year' => $year]);
        }
    }

    public function down(): void
    {
        Schema::table('iphone_models', function (Blueprint $table) {
            $table->dropColumn('release_year');
        });
    }
};
