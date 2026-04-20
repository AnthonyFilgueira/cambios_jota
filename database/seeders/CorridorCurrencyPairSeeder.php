<?php

namespace Database\Seeders;

use App\Models\Corridor;
use App\Models\Currency;
use App\Models\CurrencyPair;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorridorCurrencyPairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener corredores
        $westernUnion = Corridor::where('name', 'Western Union')->first();
        $moneyGram = Corridor::where('name', 'MoneyGram')->first();
        $remitly = Corridor::where('name', 'Remitly')->first();
        $wise = Corridor::where('name', 'Wise')->first();
        $cambioJ = Corridor::where('name', 'Cambio J Directo')->first();

        // Obtener divisas
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();
        $usd = Currency::where('code', 'USD')->first();

        // Obtener pares
        $penVes = CurrencyPair::where('from_currency_id', $pen->id)
            ->where('to_currency_id', $ves->id)->first();
        $vesPen = CurrencyPair::where('from_currency_id', $ves->id)
            ->where('to_currency_id', $pen->id)->first();
        $usdPen = CurrencyPair::where('from_currency_id', $usd->id)
            ->where('to_currency_id', $pen->id)->first();
        $penUsd = CurrencyPair::where('from_currency_id', $pen->id)
            ->where('to_currency_id', $usd->id)->first();
        $usdVes = CurrencyPair::where('from_currency_id', $usd->id)
            ->where('to_currency_id', $ves->id)->first();

        // Asignaciones de corredores a pares
        // PEN → VES: Todos los corredores (par principal)
        $penVes->corridors()->attach([
            $westernUnion->id => ['is_enabled' => true],
            $moneyGram->id => ['is_enabled' => true],
            $remitly->id => ['is_enabled' => true],
            $wise->id => ['is_enabled' => true],
            $cambioJ->id => ['is_enabled' => true],
        ]);

        // VES → PEN: Todos los corredores
        $vesPen->corridors()->attach([
            $westernUnion->id => ['is_enabled' => true],
            $moneyGram->id => ['is_enabled' => true],
            $remitly->id => ['is_enabled' => true],
            $wise->id => ['is_enabled' => true],
            $cambioJ->id => ['is_enabled' => true],
        ]);

        // USD → PEN: Western Union, MoneyGram, Wise
        $usdPen->corridors()->attach([
            $westernUnion->id => ['is_enabled' => true],
            $moneyGram->id => ['is_enabled' => true],
            $wise->id => ['is_enabled' => true],
        ]);

        // PEN → USD: Western Union, Remitly, Wise
        $penUsd->corridors()->attach([
            $westernUnion->id => ['is_enabled' => true],
            $remitly->id => ['is_enabled' => true],
            $wise->id => ['is_enabled' => true],
        ]);

        // USD → VES: Solo Western Union (par deshabilitado, pero con corredor)
        $usdVes->corridors()->attach([
            $westernUnion->id => ['is_enabled' => false],
        ]);
    }
}
