<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignment_item_exchanges', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('consignment_item_id')->constrained('consignment_stock_items')->cascadeOnDelete();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Estado anterior (antes da troca)
            $table->string('old_imei')->nullable();
            $table->string('old_serial_number')->nullable();
            $table->string('old_name');
            $table->string('old_model')->nullable();
            $table->string('old_storage')->nullable();
            $table->string('old_color')->nullable();
            $table->string('old_condition', 30)->default('new');

            // Estado novo (apos troca)
            $table->string('new_imei')->nullable();
            $table->string('new_serial_number')->nullable();
            $table->string('new_name');
            $table->string('new_model')->nullable();
            $table->string('new_storage')->nullable();
            $table->string('new_color')->nullable();
            $table->string('new_condition', 30)->default('new');

            // Detalhes da troca
            $table->string('partner_name'); // texto livre - nome do lojista
            $table->decimal('cost_adjustment', 10, 2)->default(0); // + recebi, - paguei
            $table->text('reason')->nullable();
            $table->timestamp('exchanged_at');
            $table->timestamps();

            $table->index('consignment_item_id');
            $table->index('exchanged_at');
        });

        // Adiciona o tipo "exchange" no enum da tabela consignment_stock_movements.
        // Como Laravel nao tem suporte direto a alterar enums via Schema, usamos SQL bruto.
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE consignment_stock_movements MODIFY COLUMN type ENUM('in','out','return','exchange') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE consignment_stock_movements MODIFY COLUMN type ENUM('in','out','return') NOT NULL");
        }

        Schema::dropIfExists('consignment_item_exchanges');
    }
};
