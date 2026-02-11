<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cria tabelas para o módulo de Avaliação de iPhones Seminovos
     */
    public function up(): void
    {
        // Catálogo de modelos de iPhone rastreados
        Schema::create('iphone_models', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');              // "iPhone 15 Pro Max"
            $table->string('slug')->unique();    // "iphone-15-pro-max"
            $table->json('storages');            // ["128GB","256GB","512GB","1TB"]
            $table->json('colors');              // ["Preto","Branco","Azul"]
            $table->string('search_term');       // "iphone 15 pro max"
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('active');
        });

        // Dados brutos coletados do OLX
        Schema::create('market_listings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('iphone_model_id')->constrained('iphone_models')->cascadeOnDelete();
            $table->string('storage')->nullable();
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->string('url')->nullable();
            $table->string('source')->default('olx');
            $table->string('location')->nullable();
            $table->date('scraped_at');
            $table->timestamps();

            $table->index(['iphone_model_id', 'storage', 'scraped_at']);
            $table->index('scraped_at');
            $table->index('source');
        });

        // Médias diárias calculadas
        Schema::create('price_averages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('iphone_model_id')->constrained('iphone_models')->cascadeOnDelete();
            $table->string('storage');
            $table->decimal('avg_price', 10, 2);
            $table->decimal('median_price', 10, 2);
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->decimal('suggested_buy_price', 10, 2);
            $table->integer('sample_count');
            $table->date('calculated_at');
            $table->timestamps();

            $table->unique(['iphone_model_id', 'storage', 'calculated_at']);
            $table->index('calculated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_averages');
        Schema::dropIfExists('market_listings');
        Schema::dropIfExists('iphone_models');
    }
};
