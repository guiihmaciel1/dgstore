<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE commissions MODIFY status ENUM('pending', 'approved', 'paid', 'cancelled') NOT NULL DEFAULT 'approved'"
        );

        // Comissões de vendas canceladas antes desta correção continuavam aprovadas.
        DB::table('commissions')
            ->whereIn('sale_id', DB::table('sales')->select('id')->where('payment_status', 'cancelled'))
            ->where('status', '!=', 'cancelled')
            ->update(['status' => 'cancelled', 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('commissions')
            ->where('status', 'cancelled')
            ->update(['status' => 'approved']);

        DB::statement(
            "ALTER TABLE commissions MODIFY status ENUM('pending', 'approved', 'paid') NOT NULL DEFAULT 'approved'"
        );
    }
};
