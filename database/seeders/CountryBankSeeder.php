<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BusinessAccount;
use App\Models\Country;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class CountryBankSeeder extends Seeder
{
    public function run(): void
    {
        // ─── PERÚ (Origen) ────────────────────────────────────────────────
        $peru = Country::create([
            'name'          => 'Perú',
            'code_iso'      => 'PE',
            'emoji'         => '🇵🇪',
            'currency_name' => 'PEN — Sol peruano',
            'role'          => 'origin',
            'active'        => true,
        ]);

        $bancosPeru = [
            ['name' => 'BCP — Banco de Crédito del Perú',   'swift_code' => 'BCPLPEPL'],
            ['name' => 'Interbank',                          'swift_code' => 'BINPPEPL'],
            ['name' => 'BBVA Perú',                         'swift_code' => 'BSCHPEPL'],
            ['name' => 'Scotiabank Perú',                   'swift_code' => 'BSUDPEPL'],
            ['name' => 'Yape / BCP',                        'swift_code' => null],
            ['name' => 'Plin / Interbank',                  'swift_code' => null],
            ['name' => 'Banco de la Nación',                'swift_code' => 'BNACPEPX'],
        ];

        $banksPeru = [];
        foreach ($bancosPeru as $b) {
            $banksPeru[$b['name']] = Bank::create([
                'country_id' => $peru->id,
                'name'       => $b['name'],
                'swift_code' => $b['swift_code'],
                'active'     => true,
            ]);
        }

        // Cuentas del negocio en Perú (donde recibe Cambio J)
        $cuentaPrincipal = BusinessAccount::create([
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['BCP — Banco de Crédito del Perú']->id,
            'account_number' => '1940-4872345-0-53',
            'account_type'   => 'ahorro',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'BCP Principal',
            'active'         => true,
        ]);

        $cuentaInterbank = BusinessAccount::create([
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['Interbank']->id,
            'account_number' => '898-0000012345-67',
            'account_type'   => 'corriente',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'Interbank Corriente',
            'active'         => true,
        ]);

        $cuentaBBVA = BusinessAccount::create([
            'country_id'     => $peru->id,
            'bank_id'        => $banksPeru['BBVA Perú']->id,
            'account_number' => '0011-0111-0100123456',
            'account_type'   => 'ahorro',
            'account_holder' => 'Cambio J SAC',
            'dni_ruc'        => '20601234567',
            'alias'          => 'BBVA Ahorros',
            'active'         => true,
        ]);

        // ─── VENEZUELA (Destino) ──────────────────────────────────────────
        $venezuela = Country::create([
            'name'          => 'Venezuela',
            'code_iso'      => 'VE',
            'emoji'         => '🇻🇪',
            'currency_name' => 'VES — Bolívar soberano',
            'role'          => 'destination',
            'active'        => true,
        ]);

        $bancosVenezuela = [
            ['name' => 'Banco de Venezuela',    'swift_code' => 'BVNAVE2B'],
            ['name' => 'Banesco',               'swift_code' => 'BSCRVE2C'],
            ['name' => 'Mercantil',             'swift_code' => null],
            ['name' => 'Venezuela Bicentenario','swift_code' => null],
            ['name' => 'Banco del Tesoro',      'swift_code' => null],
            ['name' => 'Bancamiga',             'swift_code' => null],
            ['name' => 'Provincial (BBVA)',     'swift_code' => 'BSCHVECA'],
            ['name' => 'Exterior',              'swift_code' => null],
        ];

        foreach ($bancosVenezuela as $b) {
            Bank::create([
                'country_id' => $venezuela->id,
                'name'       => $b['name'],
                'swift_code' => $b['swift_code'],
                'active'     => true,
            ]);
        }

        // ─── ASIGNAR CUENTAS A VENDEDORES DEMO ───────────────────────────
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            // Todos los vendedores reciben la cuenta principal BCP
            $cuentaPrincipal->sellers()->attach($seller->id);

            // Vendedores impares también reciben Interbank
            if ($seller->id % 2 !== 0) {
                $cuentaInterbank->sellers()->attach($seller->id);
            }
        }

        $this->command->info('✅ Países y bancos creados: Perú (' . count($bancosPeru) . ' bancos) + Venezuela (' . count($bancosVenezuela) . ' bancos)');
        $this->command->info('💳 3 cuentas del negocio creadas en Perú');
        $this->command->info('🔗 Cuentas asignadas a ' . $sellers->count() . ' vendedores demo');
    }
}
