<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignUlid('seller_id')->nullable()->after('seller_name')
                ->constrained('users')->nullOnDelete();
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->decimal('profit_commission', 10, 2)->default(0)->after('commission_amount');
            $table->decimal('tradein_commission', 10, 2)->default(0)->after('profit_commission');
            $table->decimal('accessory_commission', 10, 2)->default(0)->after('tradein_commission');
            $table->decimal('sale_profit', 10, 2)->nullable()->after('accessory_commission');
            $table->string('customer_name')->nullable()->after('sale_profit');
            $table->string('product_summary')->nullable()->after('customer_name');
        });

        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE commissions MODIFY commission_type ENUM('percentage', 'fixed', 'dynamic') DEFAULT 'percentage'"
        );
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn('seller_id');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn([
                'profit_commission',
                'tradein_commission',
                'accessory_commission',
                'sale_profit',
                'customer_name',
                'product_summary',
            ]);
        });
    }
};
