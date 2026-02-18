<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->string('payment_method')->default('pix')->after('status');
            $table->string('pix_code')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('pix_code');
        });

        DB::table('b2b_orders')->where('status', 'received')->update(['status' => 'pending_payment']);
        DB::table('b2b_orders')->where('status', 'shipped')->update(['status' => 'ready']);
    }

    public function down(): void
    {
        DB::table('b2b_orders')->where('status', 'pending_payment')->update(['status' => 'received']);
        DB::table('b2b_orders')->where('status', 'paid')->update(['status' => 'received']);
        DB::table('b2b_orders')->where('status', 'ready')->update(['status' => 'shipped']);

        Schema::table('b2b_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'pix_code', 'paid_at']);
        });
    }
};
