<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('color', 7)->default('#6b7280');
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->timestamps();

            $table->index('position');
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignUlid('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignUlid('pipeline_stage_id')->constrained('pipeline_stages')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('product_interest')->nullable(); // Ex: "iPhone 16 Pro Max 256GB"
            $table->decimal('value', 12, 2)->nullable();
            $table->string('phone')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->unsignedInteger('position')->default(0); // Ordem dentro do stage
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->timestamps();

            $table->index(['pipeline_stage_id', 'position']);
            $table->index(['user_id', 'pipeline_stage_id']);
            $table->index('customer_id');
        });

        Schema::create('deal_activities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type'); // call, whatsapp, note, stage_change, created, won, lost
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // dados extras (ex: stage anterior/novo)
            $table->timestamps();

            $table->index(['deal_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('pipeline_stages');
    }
};
