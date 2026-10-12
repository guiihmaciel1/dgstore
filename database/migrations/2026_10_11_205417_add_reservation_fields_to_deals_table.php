<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('value');
            $table->dateTime('deposit_paid_at')->nullable()->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'deposit_paid_at']);
        });
    }
};
