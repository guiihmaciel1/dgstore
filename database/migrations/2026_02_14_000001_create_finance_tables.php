<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('type')->default('cash'); // cash, bank, digital_wallet
            $table->decimal('initial_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->string('color', 7)->default('#111827');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('financial_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('type'); // income, expense
            $table->string('color', 7)->default('#6b7280');
            $table->string('icon')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->foreignUlid('category_id')->constrained('financial_categories')->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type'); // income, expense
            $table->string('status')->default('pending'); // pending, paid, overdue, cancelled
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->date('due_date');
            $table->datetime('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('reference_type')->nullable(); // Sale, Reservation, ImportOrder
            $table->ulid('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('paid_at');
        });

        Schema::create('account_transfers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('from_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->foreignUlid('to_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->datetime('transferred_at');
            $table->timestamps();

            $table->index('transferred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transfers');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('financial_categories');
        Schema::dropIfExists('financial_accounts');
    }
};
