<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->timestamp('receipt_sent_at')->nullable()->after('sold_at');
            $table->foreignUlid('receipt_sent_by')->nullable()->after('receipt_sent_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('receipt_sent_by');
            $table->dropColumn('receipt_sent_at');
        });
    }
};
