<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message');
            $table->string('media_url')->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignUlid('created_by')->constrained('users');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('delivered_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('campaign_customer_tag', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained('whatsapp_campaigns')->cascadeOnDelete();
            $table->foreignUlid('customer_tag_id')->constrained('customer_tags')->cascadeOnDelete();
            $table->primary(['campaign_id', 'customer_tag_id']);
        });

        Schema::create('campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('whatsapp_campaigns')->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained('customers');
            $table->string('phone');
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_recipients');
        Schema::dropIfExists('campaign_customer_tag');
        Schema::dropIfExists('whatsapp_campaigns');
    }
};
