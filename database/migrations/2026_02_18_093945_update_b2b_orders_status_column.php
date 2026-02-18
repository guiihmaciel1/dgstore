<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE b2b_orders MODIFY COLUMN status ENUM('pending_payment','paid','separating','ready','completed','cancelled') DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE b2b_orders MODIFY COLUMN status ENUM('received','separating','shipped','completed','cancelled') DEFAULT 'received'");
    }
};
