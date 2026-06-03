<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BusinessAccount;
use App\Models\Corridor;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyPair;
use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// CambiosJota — solo corredor Perú ↔ Venezuela, completamente autocontenido e idempotente
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

        // 4. Corredores genéricos
        $corridorData = [
            ['name' => 'Western Union',    'description' => 'Líder mundial en transferencias de dinero y pagos internacionales.'],
            ['name' => 'MoneyGram',        'description' => 'Servicio global de transferencias de dinero rápidas y seguras.'],
            ['name' => 'Remitly',          'description' => 'Plataforma digital de remesas internacionales con tarifas competitivas.'],
            ['name' => 'Wise',             'description' => 'Transferencias internacionales con tipo de cambio real y tarifas bajas.'],
            ['name' => 'Cambio J Directo', 'description' => 'Servicio directo de cambio de divisas Cambio J con las mejores tasas.'],
        ];
        foreach ($corridorData as $data) {
            Corridor::updateOrCreate(['name' => $data['name']], array_merge($data, ['is_active' => true]));
        }

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

        // 8. País Perú + bancos + cuentas del negocio
        $peru = Country::updateOrCreate(['code_iso' => 'PE'], [
            'name'          => 'Perú',
            'emoji'         => '🇵🇪',
            'currency_name' => 'PEN — Sol peruano',
            'role'          => 'origin',
            'active'        => true,
        ]);

        $bancosPeru = [
            ['name' => 'BCP — Banco de Crédito del Perú', 'swift_code' => 'BCPLPEPL'],
            ['name' => 'Interbank',                        'swift_code' => 'BINPPEPL'],
            ['name' => 'BBVA Perú',                        'swift_code' => 'BSCHPEPL'],
            ['name' => 'Scotiabank Perú',                  'swift_code' => 'BSUDPEPL'],
            ['name' => 'Yape / BCP',                       'swift_code' => null],
            ['name' => 'Plin / Interbank',                 'swift_code' => null],
            ['name' => 'Banco de la Nación',               'swift_code' => 'BNACPEPX'],
        ];

        $banksPeru = [];
        foreach ($bancosPeru as $b) {
            $banksPeru[$b['name']] = Bank::updateOrCreate(
                ['country_id' => $peru->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        BusinessAccount::updateOrCreate(['account_number' => '1940-4872345-0-53'], [
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['BCP — Banco de Crédito del Perú']->id,
            'account_type'   => 'ahorro',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'BCP Principal',
            'active'         => true,
        ]);

        BusinessAccount::updateOrCreate(['account_number' => '898-0000012345-67'], [
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['Interbank']->id,
            'account_type'   => 'corriente',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'Interbank Corriente',
            'active'         => true,
        ]);

        BusinessAccount::updateOrCreate(['account_number' => '0011-0111-0100123456'], [
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['BBVA Perú']->id,
            'account_type'   => 'ahorro',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'BBVA Ahorros',
            'active'         => true,
        ]);

        // 9. País Venezuela + bancos
        $venezuela = Country::updateOrCreate(['code_iso' => 'VE'], [
            'name'          => 'Venezuela',
            'emoji'         => '🇻🇪',
            'currency_name' => 'VES — Bolívar soberano',
            'role'          => 'destination',
            'active'        => true,
        ]);

        $bancosVenezuela = [
            ['name' => 'Banco de Venezuela',     'swift_code' => 'BVNAVE2B'],
            ['name' => 'Banesco',                'swift_code' => 'BSCRVE2C'],
            ['name' => 'Mercantil',              'swift_code' => null],
            ['name' => 'Venezuela Bicentenario', 'swift_code' => null],
            ['name' => 'Banco del Tesoro',       'swift_code' => null],
            ['name' => 'Bancamiga',              'swift_code' => null],
            ['name' => 'Provincial (BBVA)',       'swift_code' => 'BSCHVECA'],
            ['name' => 'Exterior',               'swift_code' => null],
        ];

        foreach ($bancosVenezuela as $b) {
            Bank::updateOrCreate(
                ['country_id' => $venezuela->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // 10. Tipos de documento y métodos de pago (PE + VE) — ya son idempotentes
        $this->call([
            DocumentTypeSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        $this->command->info('✅ [CambiosJota] Database seeding completed!');
    }
}
