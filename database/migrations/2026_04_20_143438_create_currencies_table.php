<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique()->comment('Código ISO (PEN, VES, USD)');
            $table->string('name')->comment('Nombre completo (Nuevo Sol Peruano)');
            $table->string('symbol', 10)->comment('Símbolo (S/., Bs., $)');
            $table->string('country')->comment('País de origen');
            $table->boolean('is_active')->default(true)->comment('Estado activo/inactivo');
            $table->string('flag_emoji', 10)->nullable()->comment('Emoji de bandera (opcional)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
