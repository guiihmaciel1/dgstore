<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN category ENUM(
            'smartphone',
            'tablet',
            'notebook',
            'smartwatch',
            'headphone',
            'speaker',
            'console',
            'camera',
            'perfume',
            'charger',
            'cable',
            'case',
            'accessory',
            'service'
        ) DEFAULT 'smartphone'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN category ENUM(
            'smartphone',
            'tablet',
            'notebook',
            'smartwatch',
            'headphone',
            'speaker',
            'console',
            'camera',
            'perfume',
            'accessory',
            'service'
        ) DEFAULT 'smartphone'");
    }
};
