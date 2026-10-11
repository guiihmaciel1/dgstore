<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->unsignedInteger('unread_wa_count')->default(0)->after('last_interaction_at');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->text('transcription')->nullable()->after('message_body');
            $table->string('delivery_status', 20)->nullable()->after('raw_payload');
            $table->string('direction', 10)->default('inbound')->after('delivery_status');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('wa_avatar_url')->nullable()->after('instagram');
            $table->timestamp('wa_avatar_cached_at')->nullable()->after('wa_avatar_url');
            $table->boolean('wa_is_business')->default(false)->after('wa_avatar_cached_at');
            $table->string('wa_business_name')->nullable()->after('wa_is_business');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('unread_wa_count');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn(['transcription', 'delivery_status', 'direction']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['wa_avatar_url', 'wa_avatar_cached_at', 'wa_is_business', 'wa_business_name']);
        });
    }
};
