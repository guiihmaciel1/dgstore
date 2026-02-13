<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Permitir reservas sem produto do estoque (ex: cotações de fornecedores)
            $table->foreignUlid('product_id')->nullable()->change();
            $table->string('product_description')->nullable()->after('product_id');
            $table->string('source')->default('stock')->after('product_description'); // stock, quotation, manual
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['product_description', 'source']);
        });
    }
};
