<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tac_devices', function (Blueprint $table) {
            $table->id();
            $table->string('tac', 8)->unique();
            $table->string('brand', 100)->index();
            $table->string('model', 150);
            $table->string('device_type', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tac_devices');
    }
};
