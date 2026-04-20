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
            'currency_pair_id' => $penVesPair->id,

            // Tasas de referencia BCV (iguales para todos los pares)
            'usd_rate' => 479.77750,            // Tasa BCV USD→VES (Bs./USD)
            'eur_rate' => 565.98392,            // Tasa BCV EUR→VES (Bs./EUR)

            // Tasa específica del par PEN→VES
            'ves_rate' => 173.71000,            // 1 PEN = 173.71 VES

            'is_active' => true,
        ]);

        $this->command->info('✅ Tasa inicial PEN→VES creada con tasas BCV de referencia');
    }
}
