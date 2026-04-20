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
        Schema::create('exchange_rate_history', function (Blueprint $table) {
            $table->id();

            // Relación con tasa de cambio
            $table->foreignId('exchange_rate_id')
                ->constrained('exchange_rates')
                ->onDelete('cascade');

            // Usuario que realizó el cambio (nullable para cambios del sistema)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Tipo de acción
            $table->enum('action', ['created', 'updated', 'activated', 'deactivated', 'deleted'])
                ->comment('Tipo de cambio realizado');

            // Valores anteriores y nuevos (JSON para flexibilidad)
            $table->json('old_values')->nullable()->comment('Valores antes del cambio');
            $table->json('new_values')->nullable()->comment('Valores después del cambio');

            // Información de auditoría
            $table->string('ip_address', 45)->nullable()->comment('IP desde donde se hizo el cambio');
            $table->text('user_agent')->nullable()->comment('Navegador/cliente usado');

            // Descripción opcional del cambio
            $table->text('description')->nullable()->comment('Descripción del cambio (opcional)');

            // Timestamp del cambio
            $table->timestamp('created_at');

            // Índices para búsquedas rápidas
            $table->index('exchange_rate_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rate_history');
    }
};
