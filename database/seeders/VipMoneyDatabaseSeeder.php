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

// VipMoney — todos los corredores activos, completamente autocontenido e idempotente
class VipMoneyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 [VipMoney] Seeding database...');

        // 1. Roles y permisos
        $this->call([RolesAndPermissionsSeeder::class]);

        // 2. Administrador VipMoney
        $admin = User::updateOrCreate(
            ['email' => 'admin@vipmoney.com'],
            [
                'name'              => 'Admin VipMoney',
                'password'          => Hash::make('VipMoney2026!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        // 3. Divisas base (todas las monedas que usa el sistema)
        $currencies = [
            ['code' => 'PEN', 'name' => 'Sol Peruano',           'symbol' => 'S/.',  'country' => 'Perú',            'flag_emoji' => '🇵🇪'],
            ['code' => 'VES', 'name' => 'Bolívar Venezolano',    'symbol' => 'Bs.S', 'country' => 'Venezuela',       'flag_emoji' => '🇻🇪'],
            ['code' => 'USD', 'name' => 'Dólar Estadounidense',  'symbol' => '$',    'country' => 'Estados Unidos',  'flag_emoji' => '🇺🇸'],
            ['code' => 'EUR', 'name' => 'Euro',                  'symbol' => '€',    'country' => 'Unión Europea',   'flag_emoji' => '🇪🇺'],
            ['code' => 'COP', 'name' => 'Peso Colombiano',       'symbol' => '$',    'country' => 'Colombia',        'flag_emoji' => '🇨🇴'],
            ['code' => 'ARS', 'name' => 'Peso Argentino',        'symbol' => '$',    'country' => 'Argentina',       'flag_emoji' => '🇦🇷'],
            ['code' => 'CLP', 'name' => 'Peso Chileno',          'symbol' => '$',    'country' => 'Chile',           'flag_emoji' => '🇨🇱'],
            ['code' => 'BRL', 'name' => 'Real Brasileño',        'symbol' => 'R$',   'country' => 'Brasil',          'flag_emoji' => '🇧🇷'],
        ];

        $currencyMap = [];
        foreach ($currencies as $c) {
            $currencyMap[$c['code']] = Currency::updateOrCreate(
                ['code' => $c['code']],
                ['name' => $c['name'], 'symbol' => $c['symbol'], 'country' => $c['country'], 'is_active' => true, 'flag_emoji' => $c['flag_emoji']]
            );
        }

        // 4. Corredores genéricos
        $corridorData = [
            ['name' => 'Western Union',    'description' => 'Líder mundial en transferencias de dinero y pagos internacionales.'],
            ['name' => 'MoneyGram',        'description' => 'Servicio global de transferencias de dinero rápidas y seguras.'],
            ['name' => 'Remitly',          'description' => 'Plataforma digital de remesas internacionales con tarifas competitivas.'],
            ['name' => 'Wise',             'description' => 'Transferencias internacionales con tipo de cambio real y tarifas bajas.'],
            ['name' => 'VipMoney Directo', 'description' => 'Servicio directo de cambio de divisas VipMoney con las mejores tasas.'],
        ];
        foreach ($corridorData as $data) {
            Corridor::updateOrCreate(['name' => $data['name']], array_merge($data, ['is_active' => true]));
        }

        // 5. País Perú + bancos
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
            'account_holder' => 'VipMoney SAC',
            'dni_ruc'        => '20709876543',
            'alias'          => 'BCP Principal',
            'active'         => true,
        ]);

        BusinessAccount::updateOrCreate(['account_number' => '898-0000098765-43'], [
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['Interbank']->id,
            'account_type'   => 'corriente',
            'account_holder' => 'VipMoney SAC',
            'dni_ruc'        => '20709876543',
            'alias'          => 'Interbank Corriente',
            'active'         => true,
        ]);

        // 6. País Venezuela + bancos
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
            ['name' => 'Provincial (BBVA)',      'swift_code' => 'BSCHVECA'],
            ['name' => 'Exterior',               'swift_code' => null],
        ];

        foreach ($bancosVenezuela as $b) {
            Bank::updateOrCreate(
                ['country_id' => $venezuela->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // 7. Vincular divisas base a sus países
        $currencyMap['PEN']->update(['country_id' => $peru->id]);
        $currencyMap['VES']->update(['country_id' => $venezuela->id]);

        // 8. Par y tasa PEN → VES
        $penVes = CurrencyPair::updateOrCreate(
            ['from_currency_id' => $currencyMap['PEN']->id, 'to_currency_id' => $currencyMap['VES']->id],
            ['is_active' => true]
        );

        $vesPen = CurrencyPair::updateOrCreate(
            ['from_currency_id' => $currencyMap['VES']->id, 'to_currency_id' => $currencyMap['PEN']->id],
            ['is_active' => true]
        );

        $syncData = Corridor::all()->mapWithKeys(fn($c) => [$c->id => ['is_enabled' => true]])->toArray();
        $penVes->corridors()->sync($syncData);
        $vesPen->corridors()->sync($syncData);

        ExchangeRate::updateOrCreate(
            ['currency_pair_id' => $penVes->id],
            ['ves_rate' => 173.71, 'usd_rate' => 479.78, 'eur_rate' => 565.98, 'is_active' => true]
        );

        // 9. Corredores adicionales: Chile + Colombia + Argentina + Brasil
        $this->call([
            MultiCorridorSeeder::class,
            MoreCorridorsSeeder::class,
        ]);

        // 10. Tipos de cuenta, documento y métodos de pago
        $this->call([
            AccountTypeSeeder::class,
            DocumentTypeSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        $this->command->info('✅ [VipMoney] Database seeding completed!');
    }
}
