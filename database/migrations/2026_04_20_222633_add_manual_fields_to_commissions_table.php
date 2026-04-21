<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('commission_amount');
            $table->boolean('is_manual')->default(false)->after('description');
        });

        // Tornar sale_id, sale_number e sale_total nullable para comissões manuais
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignUlid('sale_id')->nullable()->change();
            $table->string('sale_number')->nullable()->change();
            $table->decimal('sale_total', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_manual']);
            $table->foreignUlid('sale_id')->nullable(false)->change();
            $table->string('sale_number')->nullable(false)->change();
            $table->decimal('sale_total', 10, 2)->nullable(false)->change();
        });
    }
};
