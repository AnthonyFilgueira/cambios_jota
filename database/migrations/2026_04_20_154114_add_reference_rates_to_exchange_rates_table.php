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
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Relación con pares de divisas (REQ 6)
            $table->foreignId('currency_pair_id')->nullable()->constrained()->onDelete('cascade');

            // Sistema de márgenes (REQ 7)
            $table->decimal('base_rate', 10, 5)->nullable()->comment('Tasa base del mercado');
            $table->enum('margin_type', ['none', 'percentage', 'fixed'])->default('none');
            $table->decimal('margin_value', 5, 2)->default(0)->comment('Margen de ganancia');
            $table->decimal('final_rate', 10, 5)->nullable()->comment('Tasa final al cliente (base + margen)');

            // Auditoría
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable()->comment('Motivo del cambio');

            // NOTA: Se mantienen usd_rate, eur_rate, ves_rate para compatibilidad legacy
            // Estos campos se usarán solo para el par PEN→VES como referencias BCV
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->dropForeign(['currency_pair_id']);
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn([
                'currency_pair_id',
                'base_rate',
                'margin_type',
                'margin_value',
                'final_rate',
                'last_updated_by',
                'notes'
            ]);
        });
    }
};
