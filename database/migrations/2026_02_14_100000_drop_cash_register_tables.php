<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cash_register_entries');
        Schema::dropIfExists('cash_registers');
    }

    public function down(): void
    {
        // Tabelas do cash-register foram substituídas pelo módulo financeiro.
        // Não há necessidade de recriá-las.
    }
};
