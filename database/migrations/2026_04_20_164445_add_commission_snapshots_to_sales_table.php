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
        Schema::table('sales', function (Blueprint $table) {
            // NOTA: Los campos de comisiones ya existen (migración 2026_04_20_082342)
            // Solo agregamos los snapshots de tasas para trazabilidad

            // Snapshots de tasas (para trazabilidad y auditoría)
            $table->decimal('usd_rate_snapshot', 10, 5)->nullable()
                  ->after('admin_commission_amount')
                  ->comment('Tasa USD BCV usada al momento de la venta');

            $table->decimal('eur_rate_snapshot', 10, 5)->nullable()
                  ->after('usd_rate_snapshot')
                  ->comment('Tasa EUR BCV usada al momento de la venta');

            $table->decimal('ves_rate_snapshot', 10, 5)->nullable()
                  ->after('eur_rate_snapshot')
                  ->comment('Tasa VES/PEN usada al momento de la venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'usd_rate_snapshot',
                'eur_rate_snapshot',
                'ves_rate_snapshot',
            ]);
        });
    }
};
