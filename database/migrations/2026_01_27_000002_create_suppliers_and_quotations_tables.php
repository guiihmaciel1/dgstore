<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration para criação das tabelas de fornecedores e cotações
     */
    public function up(): void
    {
        // Tabela de fornecedores
        Schema::create('suppliers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('cnpj', 18)->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('active');
        });

        // Tabela de cotações
        Schema::create('quotations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignUlid('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('product_name'); // Nome livre do produto (sempre preenchido)
            $table->decimal('unit_price', 10, 2);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit')->default('un'); // un, kg, cx, etc.
            $table->date('quoted_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('quoted_at');
            $table->index('product_name');
            $table->index(['supplier_id', 'quoted_at']);
            $table->index(['product_id', 'quoted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('suppliers');
    }
};
