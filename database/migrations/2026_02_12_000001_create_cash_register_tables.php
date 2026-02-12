<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('opened_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('closing_balance', 10, 2)->nullable();
            $table->decimal('expected_balance', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('opened_at');
        });

        Schema::create('cash_register_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('cash_register_id')->constrained('cash_registers')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['sale', 'withdrawal', 'supply', 'trade_in', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('description');
            $table->ulid('reference_id')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_entries');
        Schema::dropIfExists('cash_registers');
    }
};
