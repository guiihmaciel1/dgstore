<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('source')->nullable()->after('lost_reason');
            $table->json('source_metadata')->nullable()->after('source');

            $table->index('source');
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('wa_message_id')->unique();
            $table->string('from_phone', 20);
            $table->string('from_name')->nullable();
            $table->string('message_type', 30)->default('text');
            $table->text('message_body')->nullable();
            $table->string('referral_source')->nullable();
            $table->string('referral_headline')->nullable();
            $table->json('raw_payload')->nullable();
            $table->foreignUlid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->timestamps();

            $table->index('from_phone');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');

        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'source_metadata']);
        });
    }
};
