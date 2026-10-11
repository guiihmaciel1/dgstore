<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negotiation_snapshots', function (Blueprint $table) {
            $table->foreignUlid('deal_id')->nullable()->after('sale_id')->constrained('deals')->nullOnDelete();
            $table->index('deal_id');
        });
    }

    public function down(): void
    {
        Schema::table('negotiation_snapshots', function (Blueprint $table) {
            $table->dropForeign(['deal_id']);
            $table->dropColumn('deal_id');
        });
    }
};
