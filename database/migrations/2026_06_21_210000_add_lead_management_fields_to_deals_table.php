<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('lead_source')->nullable()->after('source_metadata');
            $table->string('temperature')->default('warm')->after('lead_source');
            $table->string('next_action')->nullable()->after('temperature');
            $table->timestamp('next_action_at')->nullable()->after('next_action');
            $table->timestamp('last_interaction_at')->nullable()->after('next_action_at');

            $table->index('lead_source');
            $table->index('temperature');
            $table->index('last_interaction_at');
            $table->index('next_action_at');
        });

        DB::table('deals')
            ->where('source', 'whatsapp')
            ->whereNull('lead_source')
            ->update(['lead_source' => 'whatsapp']);

        DB::table('deals')
            ->whereNull('last_interaction_at')
            ->update(['last_interaction_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['lead_source']);
            $table->dropIndex(['temperature']);
            $table->dropIndex(['last_interaction_at']);
            $table->dropIndex(['next_action_at']);

            $table->dropColumn([
                'lead_source',
                'temperature',
                'next_action',
                'next_action_at',
                'last_interaction_at',
            ]);
        });
    }
};
