<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'PEN',
                'name' => 'Sol Peruano',
                'symbol' => 'S/.',
                'country' => 'Perú',
                'is_active' => true,
                'flag_emoji' => '🇵🇪',
            ],
            [
                'code' => 'VES',
                'name' => 'Bolívar Venezolano',
                'symbol' => 'Bs.',
                'country' => 'Venezuela',
                'is_active' => true,
                'flag_emoji' => '🇻🇪',
            ],
            [
                'code' => 'USD',
                'name' => 'Dólar Estadounidense',
                'symbol' => '$',
                'country' => 'Estados Unidos',
                'is_active' => true,
                'flag_emoji' => '🇺🇸',
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'country' => 'Unión Europea',
                'is_active' => true,
                'flag_emoji' => '🇪🇺',
            ],
            [
                'code' => 'COP',
                'name' => 'Peso Colombiano',
                'symbol' => '$',
                'country' => 'Colombia',
                'is_active' => true,
                'flag_emoji' => '🇨🇴',
            ],
            [
                'code' => 'ARS',
                'name' => 'Peso Argentino',
                'symbol' => '$',
                'country' => 'Argentina',
                'is_active' => true,
                'flag_emoji' => '🇦🇷',
            ],
            [
                'code' => 'CLP',
                'name' => 'Peso Chileno',
                'symbol' => '$',
                'country' => 'Chile',
                'is_active' => true,
                'flag_emoji' => '🇨🇱',
            ],
            [
                'code' => 'BRL',
                'name' => 'Real Brasileño',
                'symbol' => 'R$',
                'country' => 'Brasil',
                'is_active' => true,
                'flag_emoji' => '🇧🇷',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
