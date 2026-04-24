<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_checklists', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->json('device_info')->nullable();
            $table->json('sections');
            $table->unsignedSmallInteger('total_items')->default(0);
            $table->unsignedSmallInteger('passed_items')->default(0);
            $table->unsignedSmallInteger('failed_items')->default(0);
            $table->enum('status', ['approved', 'failed', 'incomplete'])->default('incomplete');
            $table->foreignUlid('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignUlid('checklist_id')->nullable()->after('reserved_by')->constrained('device_checklists')->nullOnDelete();
        });

        Schema::table('trade_ins', function (Blueprint $table) {
            $table->foreignUlid('checklist_id')->nullable()->after('product_id')->constrained('device_checklists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trade_ins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checklist_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('checklist_id');
        });

        Schema::dropIfExists('device_checklists');
    }
};
