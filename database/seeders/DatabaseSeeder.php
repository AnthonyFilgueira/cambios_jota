<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding database...');

        // 1. Usuario principal del sistema
        User::factory()->create([
            'name' => 'abreu',
            'email' => 'cambiosjottaa@innodite.com',
            'password' => Hash::make('CambiosJota2026!'),
        ]);

        // 2. Seeders de configuración base (REQ 6 y 7)
        $this->call([
            CurrencySeeder::class,           // Divisas (PEN, VES, USD, etc.)
            CurrencyPairSeeder::class,       // Pares de conversión
            CorridorSeeder::class,           // Corredores
            CorridorCurrencyPairSeeder::class, // Asignación de pares a corredores
            ExchangeRateSeeder::class,       // Tasas de cambio activas
        ]);

        // 3. Seeder de datos de demostración (REQ 11)
        $this->call([
            DemoDataSeeder::class,           // Usuarios, vendedores, ventas
        ]);

        $this->command->info('✅ Database seeding completed!');
    }
}
