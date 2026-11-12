<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registro das mensagens de aniversário enviadas pelo WhatsApp (UazAPI).
 * Apenas cria uma tabela nova; não altera nem apaga dados existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_greetings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('phone', 20);
            $table->string('status', 10); // sent | failed
            $table->foreignUlid('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('wa_message_id')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'year']);
            $table->index(['year', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_greetings');
    }
};
