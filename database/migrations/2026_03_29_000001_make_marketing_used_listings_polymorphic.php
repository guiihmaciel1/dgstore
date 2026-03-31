<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->string('listable_type')->nullable()->after('id');
            $table->string('listable_id', 26)->nullable()->after('listable_type');
        });

        DB::table('marketing_used_listings')
            ->whereNotNull('product_id')
            ->update([
                'listable_type' => 'App\\Domain\\Product\\Models\\Product',
                'listable_id' => DB::raw('product_id'),
            ]);

        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->string('listable_type')->nullable(false)->change();
            $table->string('listable_id', 26)->nullable(false)->change();

            $table->dropForeign(['product_id']);
            $table->dropUnique(['product_id']);
            $table->dropColumn('product_id');

            $table->unique(['listable_type', 'listable_id']);
            $table->index(['listable_type', 'listable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->foreignUlid('product_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::table('marketing_used_listings')
            ->where('listable_type', 'App\\Domain\\Product\\Models\\Product')
            ->update([
                'product_id' => DB::raw('listable_id'),
            ]);

        Schema::table('marketing_used_listings', function (Blueprint $table) {
            $table->dropIndex(['listable_type', 'listable_id']);
            $table->dropUnique(['listable_type', 'listable_id']);
            $table->dropColumn(['listable_type', 'listable_id']);
            $table->unique('product_id');
        });
    }
};
