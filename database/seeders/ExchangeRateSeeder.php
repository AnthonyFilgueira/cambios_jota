<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use App\Models\Currency;
use App\Models\CurrencyPair;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener divisas (deben existir del REQ 6)
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();

        if (!$pen || !$ves) {
            $this->command->warn('⚠️  Las divisas PEN y VES deben existir primero. Ejecuta CurrencySeeder.');
            return;
        }

        // Obtener o crear par PEN → VES
        $penVesPair = CurrencyPair::firstOrCreate([
            'from_currency_id' => $pen->id,
            'to_currency_id' => $ves->id,
        ], [
            'is_active' => true,
        ]);

        // Crear tasa inicial para par PEN → VES con tasas BCV de referencia
        ExchangeRate::create([
            // Nuevo sistema (REQ 7)
            'currency_pair_id' => $penVesPair->id,
            'base_rate' => 0.1200,              // Tasa base PEN→VES (mercado paralelo)
            'margin_type' => 'percentage',
            'margin_value' => 3.00,             // 3% de margen
            'final_rate' => 0.1236,             // 0.12 + (0.12 × 0.03) = 0.1236

            // Tasas BCV de referencia (para cálculos del simulador)
            'usd_rate' => 479.77750,            // Tasa BCV USD (Bs./USD)
            'eur_rate' => 565.98392,            // Tasa BCV EUR (Bs./EUR)
            'ves_rate' => 173.71000,            // Tasa directa VES/PEN (histórica, ahora en base_rate)

            'is_active' => true,
            'notes' => 'Tasa inicial del sistema - migración de legacy',
        ]);

        $this->command->info('✅ Tasa inicial PEN→VES creada con tasas BCV de referencia');
    }
}
