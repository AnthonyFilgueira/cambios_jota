<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\CurrencyPair;
use App\Models\Corridor;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// CambiosJota — solo corredor Perú ↔ Venezuela, sin datos de demo
class CambiosJotaDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 [CambiosJota] Seeding database...');

        // 1. Roles y permisos
        $this->call([RolesAndPermissionsSeeder::class]);

        // 2. Administrador CambiosJota
        $admin = User::updateOrCreate(
            ['email' => 'cambiosjottaa@innodite.com'],
            [
                'name'              => 'abreu',
                'password'          => Hash::make('CambiosJota2026!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        // 3. Divisas: solo PEN + VES
        $pen = Currency::updateOrCreate(['code' => 'PEN'], [
            'name'       => 'Sol Peruano',
            'symbol'     => 'S/.',
            'country'    => 'Perú',
            'is_active'  => true,
            'flag_emoji' => '🇵🇪',
        ]);

        $ves = Currency::updateOrCreate(['code' => 'VES'], [
            'name'       => 'Bolívar Venezolano',
            'symbol'     => 'Bs.S',
            'country'    => 'Venezuela',
            'is_active'  => true,
            'flag_emoji' => '🇻🇪',
        ]);

        // 4. Corredores genéricos (Western Union, MoneyGram, Remitly, Wise, Cambio J Directo)
        $this->call([CorridorSeeder::class]);

        // 5. Pares de divisa: solo PEN↔VES
        $penVes = CurrencyPair::updateOrCreate(
            ['from_currency_id' => $pen->id, 'to_currency_id' => $ves->id],
            ['is_active' => true]
        );

        $vesPen = CurrencyPair::updateOrCreate(
            ['from_currency_id' => $ves->id, 'to_currency_id' => $pen->id],
            ['is_active' => true]
        );

        // 6. Asignar todos los corredores a ambos pares (habilitados)
        $syncData = Corridor::all()
            ->mapWithKeys(fn($c) => [$c->id => ['is_enabled' => true]])
            ->toArray();

        $penVes->corridors()->sync($syncData);
        $vesPen->corridors()->sync($syncData);

        // 7. Tasa de cambio PEN→VES
        ExchangeRate::updateOrCreate(
            ['currency_pair_id' => $penVes->id],
            [
                'ves_rate'  => 173.71,
                'usd_rate'  => 479.78,
                'eur_rate'  => 565.98,
                'is_active' => true,
            ]
        );

        // 8. Países, bancos y configuraciones (PE + VE únicamente)
        $this->call([
            CountryBankSeeder::class,   // Perú + Venezuela + bancos + cuentas del negocio
            DocumentTypeSeeder::class,  // Tipos de documento PE + VE
            PaymentMethodSeeder::class, // Métodos de pago (auto-filtra a países existentes)
        ]);

        $this->command->info('✅ [CambiosJota] Database seeding completed!');
    }
}
