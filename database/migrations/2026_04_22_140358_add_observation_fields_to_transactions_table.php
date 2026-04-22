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
        Schema::table('transactions', function (Blueprint $table) {
            // Modificar el enum de status para incluir 'observed'
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'observed'])->default('pending')->change();

            // Añadir campo de observación
            $table->text('observation')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Volver al enum original
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->change();

            // Eliminar campo de observación
            $table->dropColumn('observation');
        });
    }
};
