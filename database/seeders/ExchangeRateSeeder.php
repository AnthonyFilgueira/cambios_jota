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
        // Obtener divisas
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();
        $ars = Currency::where('code', 'ARS')->first();
        $clp = Currency::where('code', 'CLP')->first();

        if (!$pen || !$ves || !$ars || !$clp) {
            $this->command->warn('⚠️  Las divisas deben existir primero. Ejecuta CurrencySeeder.');
            return;
        }

        // Tasas de referencia BCV (iguales para todos)
        $usdRate = 479.78;
        $eurRate = 565.98;

        // Crear pares y tasas
        $pairs = [
            [
                'from' => $pen,
                'to' => $ves,
                'ves_rate' => 173.71,
                'is_active' => true,
            ],
            [
                'from' => $ars,
                'to' => $ves,
                'ves_rate' => 2.50,
                'is_active' => false,
            ],
            [
                'from' => $clp,
                'to' => $ves,
                'ves_rate' => 0.55,
                'is_active' => false,
            ],
        ];

        foreach ($pairs as $pairData) {
            // Crear o encontrar par
            $pair = CurrencyPair::firstOrCreate([
                'from_currency_id' => $pairData['from']->id,
                'to_currency_id' => $pairData['to']->id,
            ], [
                'is_active' => true,
            ]);

            // Crear tasa
            ExchangeRate::create([
                'currency_pair_id' => $pair->id,
                'ves_rate' => $pairData['ves_rate'],
                'usd_rate' => $usdRate,
                'eur_rate' => $eurRate,
                'is_active' => $pairData['is_active'],
            ]);

            $this->command->info("✅ Tasa {$pairData['from']->code}→{$pairData['to']->code} creada");
        }
    }
}
