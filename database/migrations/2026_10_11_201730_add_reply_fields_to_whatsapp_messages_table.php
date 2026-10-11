<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->string('reply_to_id')->nullable()->after('delivery_status');
            $table->text('reply_to_body')->nullable()->after('reply_to_id');
            $table->index('reply_to_id');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropIndex(['reply_to_id']);
            $table->dropColumn(['reply_to_id', 'reply_to_body']);
        });
    }
};
