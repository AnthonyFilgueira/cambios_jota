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

        // 1. Roles y Permisos (PRIMERO)
        $this->call([
            RolesAndPermissionsSeeder::class, // Crear roles y permisos
        ]);

        // 2. Usuario principal del sistema
        $userAbreu = User::factory()->create([
            'name' => 'abreu',
            'email' => 'cambiosjottaa@innodite.com',
            'password' => Hash::make('CambiosJota2026!'),
        ]);
        $userAbreu->assignRole('super-admin');

        // 3. Seeders de configuración base (REQ 6 y 7)
        $this->call([
            CurrencySeeder::class,           // Divisas (PEN, VES, USD, etc.)
            CurrencyPairSeeder::class,       // Pares de conversión
            CorridorSeeder::class,           // Corredores
            CorridorCurrencyPairSeeder::class, // Asignación de pares a corredores
            ExchangeRateSeeder::class,       // Tasas de cambio activas
        ]);

        // 4. Seeder de datos de demostración
        $this->call([
            DemoDataSeeder::class,           // Usuarios, vendedores, ventas
        ]);

        // 5. Países, bancos y cuentas del negocio (DESPUÉS de vendedores para asignarles cuentas)
        $this->call([
            CountryBankSeeder::class,        // Perú + Venezuela + 3 cuentas asignadas a vendedores
        ]);

        $this->command->info('✅ Database seeding completed!');
    }
}
