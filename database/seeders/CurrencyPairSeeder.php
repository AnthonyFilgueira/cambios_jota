<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\CurrencyPair;

class CurrencyPairSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener divisas
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();
        $usd = Currency::where('code', 'USD')->first();

        // Pares comunes
        $pairs = [
            [$pen->id, $ves->id, true],  // PEN → VES
            [$ves->id, $pen->id, true],  // VES → PEN
            [$usd->id, $pen->id, true],  // USD → PEN
            [$pen->id, $usd->id, true],  // PEN → USD
            [$usd->id, $ves->id, false], // USD → VES (deshabilitado)
        ];

        foreach ($pairs as [$from, $to, $active]) {
            CurrencyPair::create([
                'from_currency_id' => $from,
                'to_currency_id' => $to,
                'is_active' => $active,
            ]);
        }
    }
}
