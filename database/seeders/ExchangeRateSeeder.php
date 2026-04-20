<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tasa inicial Perú (PEN) → Venezuela (VES)
        // Basada en tasas referenciales de mercado (abril 2026)
        ExchangeRate::create([
            'usd_rate' => 3.70,      // USD/PEN (tasa referencial BCR Perú)
            'eur_rate' => 4.10,      // EUR/PEN
            'ves_rate' => 0.1200,    // VES/PEN (ajustar según mercado paralelo)
            'is_active' => true,
        ]);

        $this->command->info('✅ Tasa inicial Perú → Venezuela creada');
    }
}
