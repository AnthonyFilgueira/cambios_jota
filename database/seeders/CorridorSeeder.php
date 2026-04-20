<?php

namespace Database\Seeders;

use App\Models\Corridor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CorridorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $corridors = [
            [
                'name' => 'Western Union',
                'description' => 'Líder mundial en transferencias de dinero y pagos internacionales.',
                'is_active' => true,
            ],
            [
                'name' => 'MoneyGram',
                'description' => 'Servicio global de transferencias de dinero rápidas y seguras.',
                'is_active' => true,
            ],
            [
                'name' => 'Remitly',
                'description' => 'Plataforma digital de remesas internacionales con tarifas competitivas.',
                'is_active' => true,
            ],
            [
                'name' => 'Wise',
                'description' => 'Transferencias internacionales con tipo de cambio real y tarifas bajas.',
                'is_active' => true,
            ],
            [
                'name' => 'Cambio J Directo',
                'description' => 'Servicio directo de cambio de divisas Cambio J con las mejores tasas.',
                'is_active' => true,
            ],
        ];

        foreach ($corridors as $corridor) {
            Corridor::create($corridor);
        }
    }
}
