<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// VipMoney — todos los corredores (PE/VE/CL/CO/AR/BR), sin datos de demo
class VipMoneyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 [VipMoney] Seeding database...');

        // 1. Roles y permisos
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // 2. Usuario administrador VipMoney
        $admin = User::updateOrCreate(
            ['email' => 'admin@vipmoney.com'],
            [
                'name'              => 'Admin VipMoney',
                'password'          => Hash::make('VipMoney2026!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        // 3. Configuración base — divisas y pares Perú + Venezuela
        $this->call([
            CurrencySeeder::class,
            CurrencyPairSeeder::class,
            CorridorSeeder::class,
            CorridorCurrencyPairSeeder::class,
            ExchangeRateSeeder::class,
        ]);

        // 4. Países, bancos, tipos de documento y métodos de pago (PE + VE)
        $this->call([
            CountryBankSeeder::class,
            DocumentTypeSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        // 5. Corredores adicionales: Chile, Colombia, Argentina, Brasil
        $this->call([
            MultiCorridorSeeder::class,   // CL + CO: pares CLP→PEN, COP→VES, etc.
            MoreCorridorsSeeder::class,   // AR + BR: pares ARS→PEN, BRL→PEN, etc.
        ]);

        $this->command->info('✅ [VipMoney] Database seeding completed!');
    }
}
