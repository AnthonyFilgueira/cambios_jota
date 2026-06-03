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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

// CambiosJota — solo corredor Perú ↔ Venezuela
class CambiosJotaDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 [CambiosJota] Seeding database...');

        // 1. Verificar que las migraciones se han ejecutado
        $this->validateRequiredTables();

        // 2. Limpiar toda la base de datos
        $this->resetDatabase();

        // 3. Roles y permisos
        $this->call([RolesAndPermissionsSeeder::class]);

        // 4. Administrador CambiosJota
        $admin = User::create([
            'email'             => 'cambiosjottaa@innodite.com',
            'name'              => 'abreu',
            'password'          => Hash::make('CambiosJota2026!'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('super-admin');

        // 4. Divisas: solo PEN + VES
        $pen = Currency::create([
            'code'       => 'PEN',
            'name'       => 'Sol Peruano',
            'symbol'     => 'S/.',
            'country'    => 'Perú',
            'is_active'  => true,
            'flag_emoji' => '🇵🇪',
        ]);

        $ves = Currency::create([
            'code'       => 'VES',
            'name'       => 'Bolívar Venezolano',
            'symbol'     => 'Bs.S',
            'country'    => 'Venezuela',
            'is_active'  => true,
            'flag_emoji' => '🇻🇪',
        ]);

        // 5. Corredores
        $corridorData = [
            ['name' => 'Western Union',    'description' => 'Líder mundial en transferencias de dinero y pagos internacionales.'],
            ['name' => 'MoneyGram',        'description' => 'Servicio global de transferencias de dinero rápidas y seguras.'],
            ['name' => 'Remitly',          'description' => 'Plataforma digital de remesas internacionales con tarifas competitivas.'],
            ['name' => 'Wise',             'description' => 'Transferencias internacionales con tipo de cambio real y tarifas bajas.'],
            ['name' => 'Cambio J Directo', 'description' => 'Servicio directo de cambio de divisas Cambio J con las mejores tasas.'],
        ];
        foreach ($corridorData as $data) {
            Corridor::create(array_merge($data, ['is_active' => true]));
        }

        // 6. Par PEN → VES
        $penVes = CurrencyPair::create(['from_currency_id' => $pen->id, 'to_currency_id' => $ves->id, 'is_active' => true]);
        $vesPen = CurrencyPair::create(['from_currency_id' => $ves->id, 'to_currency_id' => $pen->id, 'is_active' => true]);

        $syncData = Corridor::all()->mapWithKeys(fn($c) => [$c->id => ['is_enabled' => true]])->toArray();
        $penVes->corridors()->sync($syncData);
        $vesPen->corridors()->sync($syncData);

        // 7. Tasa de cambio PEN → VES
        ExchangeRate::create([
            'currency_pair_id' => $penVes->id,
            'ves_rate'         => 173.71,
            'usd_rate'         => 479.78,
            'eur_rate'         => 565.98,
            'is_active'        => true,
        ]);

        // 8. País Perú + bancos + cuentas del negocio
        $peru = Country::create([
            'name'          => 'Perú',
            'code_iso'      => 'PE',
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
            $banksPeru[$b['name']] = Bank::create(['country_id' => $peru->id, 'name' => $b['name'], 'swift_code' => $b['swift_code'], 'active' => true]);
        }

        BusinessAccount::create(['country_id' => $peru->id, 'bank_id' => $banksPeru['BCP — Banco de Crédito del Perú']->id, 'account_number' => '1940-4872345-0-53', 'account_type' => 'ahorro',    'account_holder' => 'Cambio J SAC', 'dni_ruc' => '20601234567', 'alias' => 'BCP Principal',        'active' => true]);
        BusinessAccount::create(['country_id' => $peru->id, 'bank_id' => $banksPeru['Interbank']->id,                        'account_number' => '898-0000012345-67',  'account_type' => 'corriente', 'account_holder' => 'Cambio J SAC', 'dni_ruc' => '20601234567', 'alias' => 'Interbank Corriente',  'active' => true]);
        BusinessAccount::create(['country_id' => $peru->id, 'bank_id' => $banksPeru['BBVA Perú']->id,                        'account_number' => '0011-0111-0100123456','account_type' => 'ahorro',    'account_holder' => 'Cambio J SAC', 'dni_ruc' => '20601234567', 'alias' => 'BBVA Ahorros',         'active' => true]);

        // 9. País Venezuela + bancos
        $venezuela = Country::create([
            'name'          => 'Venezuela',
            'code_iso'      => 'VE',
            'emoji'         => '🇻🇪',
            'currency_name' => 'VES — Bolívar soberano',
            'role'          => 'destination',
            'active'        => true,
        ]);

        foreach ([
            ['name' => 'Banco de Venezuela',     'swift_code' => 'BVNAVE2B'],
            ['name' => 'Banesco',                'swift_code' => 'BSCRVE2C'],
            ['name' => 'Mercantil',              'swift_code' => null],
            ['name' => 'Venezuela Bicentenario', 'swift_code' => null],
            ['name' => 'Banco del Tesoro',       'swift_code' => null],
            ['name' => 'Bancamiga',              'swift_code' => null],
            ['name' => 'Provincial (BBVA)',      'swift_code' => 'BSCHVECA'],
            ['name' => 'Exterior',               'swift_code' => null],
        ] as $b) {
            Bank::create(['country_id' => $venezuela->id, 'name' => $b['name'], 'swift_code' => $b['swift_code'], 'active' => true]);
        }

        // 10. Vincular divisas a sus países
        $pen->update(['country_id' => $peru->id]);
        $ves->update(['country_id' => $venezuela->id]);

        // 11. Tipos de cuenta, documento y métodos de pago
        $this->call([
            AccountTypeSeeder::class,
            DocumentTypeSeeder::class,
            PaymentMethodSeeder::class,
        ]);

        $this->command->info('✅ [CambiosJota] Database seeding completed!');
    }

    private function validateRequiredTables(): void
    {
        $required = [
            'users', 'roles', 'permissions',
            'currencies', 'currency_pairs', 'corridors', 'countries', 'banks',
            'account_types', 'document_types', 'payment_methods',
            'exchange_rates', 'transactions',
        ];

        $missing = array_values(array_filter($required, fn($t) => !Schema::hasTable($t)));

        if (!empty($missing)) {
            throw new \RuntimeException(
                '❌ Tablas faltantes: ' . implode(', ', $missing) . "\n" .
                '   Ejecuta `php artisan migrate` antes de correr este seeder.'
            );
        }
    }

    private function resetDatabase(): void
    {
        $this->command->info('🗑️  Limpiando base de datos...');

        Schema::disableForeignKeyConstraints();

        $this->safeTruncate('transaction_incentive_rules');
        $this->safeTruncate('transaction_logs');
        $this->safeTruncate('transactions');
        $this->safeTruncate('wallet_transactions');
        $this->safeTruncate('liquidations');
        $this->safeTruncate('sale_logs');
        $this->safeTruncate('sales');
        $this->safeTruncate('audit_logs');
        $this->safeTruncate('notifications');
        $this->safeTruncate('business_account_seller');
        $this->safeTruncate('business_accounts');
        $this->safeTruncate('exchange_rate_history');
        $this->safeTruncate('exchange_rates');
        $this->safeTruncate('corridor_currency_pair');
        $this->safeTruncate('currency_pairs');
        $this->safeTruncate('incentive_rules');
        $this->safeTruncate('commission_rules');
        $this->safeTruncate('payment_methods');
        $this->safeTruncate('account_types');
        $this->safeTruncate('document_types');
        $this->safeTruncate('banks');
        $this->safeTruncate('countries');
        $this->safeTruncate('currencies');
        $this->safeTruncate('corridors');
        $this->safeTruncate('sellers');
        $this->safeTruncate('model_has_roles');
        $this->safeTruncate('model_has_permissions');
        $this->safeTruncate('role_has_permissions');
        $this->safeTruncate('roles');
        $this->safeTruncate('permissions');
        $this->safeTruncate('users');
        $this->safeTruncate('sessions');
        $this->safeTruncate('settings');

        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Base de datos limpia.');
    }

    private function safeTruncate(string $table): void
    {
        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        }
    }
}
