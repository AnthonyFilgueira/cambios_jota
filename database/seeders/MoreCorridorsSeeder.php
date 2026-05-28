<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BusinessAccount;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyPair;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\PaymentMethod;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class MoreCorridorsSeeder extends Seeder
{
    public function run(): void
    {
        // ─── PAÍSES NUEVOS ───────────────────────────────────────────────────

        $argentina = Country::firstOrCreate(
            ['code_iso' => 'AR'],
            [
                'name'          => 'Argentina',
                'emoji'         => '🇦🇷',
                'currency_name' => 'ARS — Peso argentino',
                'role'          => 'origin',
                'active'        => true,
            ]
        );

        $brasil = Country::firstOrCreate(
            ['code_iso' => 'BR'],
            [
                'name'          => 'Brasil',
                'emoji'         => '🇧🇷',
                'currency_name' => 'BRL — Real brasileño',
                'role'          => 'origin',
                'active'        => true,
            ]
        );

        // ─── VINCULAR MONEDAS A PAÍSES ───────────────────────────────────────
        Currency::where('code', 'ARS')->whereNull('country_id')->update(['country_id' => $argentina->id]);
        Currency::where('code', 'BRL')->whereNull('country_id')->update(['country_id' => $brasil->id]);

        // ─── BANCOS ARGENTINA ─────────────────────────────────────────────────
        $bancosAr = [
            ['name' => 'Banco Nación Argentina',  'swift_code' => 'NACNARBAB'],
            ['name' => 'Banco Provincia',          'swift_code' => 'BPBAARBA'],
            ['name' => 'Santander Argentina',      'swift_code' => 'BSCHARBAB'],
            ['name' => 'BBVA Argentina',           'swift_code' => 'BSCHARBAB'],
            ['name' => 'Banco Galicia',            'swift_code' => 'GALICAARBA'],
            ['name' => 'Banco Macro',              'swift_code' => 'BCOMARBA'],
            ['name' => 'Banco ICBC',               'swift_code' => 'ICBKARBAB'],
        ];

        foreach ($bancosAr as $b) {
            Bank::firstOrCreate(
                ['country_id' => $argentina->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // ─── BANCOS BRASIL ────────────────────────────────────────────────────
        $bancosBr = [
            ['name' => 'Banco do Brasil',          'swift_code' => 'BRASBRRJBHE'],
            ['name' => 'Caixa Econômica Federal',  'swift_code' => 'CEFXBRSP'],
            ['name' => 'Bradesco',                 'swift_code' => 'BBDEBRSP'],
            ['name' => 'Itaú Unibanco',            'swift_code' => 'ITAUBRSP'],
            ['name' => 'Santander Brasil',         'swift_code' => 'BSCHBRSP'],
            ['name' => 'Nubank',                   'swift_code' => null],
            ['name' => 'Inter',                    'swift_code' => null],
        ];

        foreach ($bancosBr as $b) {
            Bank::firstOrCreate(
                ['country_id' => $brasil->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // ─── TIPOS DE DOCUMENTO ARGENTINA ─────────────────────────────────────
        $docsAr = [
            ['code' => 'DNI',  'name' => 'Documento Nacional de Identidad',          'placeholder' => '12345678'],
            ['code' => 'CUIT', 'name' => 'Clave Única de Identificación Tributaria', 'placeholder' => '20-12345678-9'],
            ['code' => 'CUIL', 'name' => 'Clave Única de Identificación Laboral',    'placeholder' => '20-12345678-9'],
        ];

        foreach ($docsAr as $d) {
            DocumentType::firstOrCreate(
                ['country_id' => $argentina->id, 'code' => $d['code']],
                array_merge($d, ['country_id' => $argentina->id, 'prefix' => null, 'active' => true])
            );
        }

        // ─── TIPOS DE DOCUMENTO BRASIL ─────────────────────────────────────────
        $docsBr = [
            ['code' => 'CPF',  'name' => 'Cadastro de Pessoas Físicas',         'placeholder' => '123.456.789-00'],
            ['code' => 'CNPJ', 'name' => 'Cadastro Nacional da Pessoa Jurídica','placeholder' => '12.345.678/0001-99'],
            ['code' => 'RG',   'name' => 'Registro Geral',                      'placeholder' => '12.345.678-9'],
        ];

        foreach ($docsBr as $d) {
            DocumentType::firstOrCreate(
                ['country_id' => $brasil->id, 'code' => $d['code']],
                array_merge($d, ['country_id' => $brasil->id, 'prefix' => null, 'active' => true])
            );
        }

        // ─── MÉTODOS DE PAGO ARGENTINA ────────────────────────────────────────
        $methodsAr = [
            ['code' => 'transferencia_bancaria', 'name' => 'Transferencia Bancaria'],
            ['code' => 'cvu_alias',              'name' => 'CVU / Alias'],
        ];

        foreach ($methodsAr as $m) {
            PaymentMethod::firstOrCreate(
                ['country_id' => $argentina->id, 'code' => $m['code']],
                ['name' => $m['name'], 'active' => true]
            );
        }

        // ─── MÉTODOS DE PAGO BRASIL ───────────────────────────────────────────
        $methodsBr = [
            ['code' => 'transferencia_bancaria', 'name' => 'Transferencia Bancaria'],
            ['code' => 'pix',                    'name' => 'PIX'],
        ];

        foreach ($methodsBr as $m) {
            PaymentMethod::firstOrCreate(
                ['country_id' => $brasil->id, 'code' => $m['code']],
                ['name' => $m['name'], 'active' => true]
            );
        }

        // ─── BUSINESS ACCOUNTS — CUENTAS DEL NEGOCIO ─────────────────────────
        $sellers = Seller::all();

        $businessAccounts = [
            // Argentina
            [
                'country'        => $argentina,
                'bank_name'      => 'Banco Nación Argentina',
                'account_number' => '123456789001',
                'account_type'   => 'ahorro',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Nación AR',
            ],
            [
                'country'        => $argentina,
                'bank_name'      => 'Banco Galicia',
                'account_number' => '0540123456789',
                'account_type'   => 'corriente',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Galicia AR',
            ],
            // Brasil
            [
                'country'        => $brasil,
                'bank_name'      => 'Banco do Brasil',
                'account_number' => '12345-6',
                'account_type'   => 'ahorro',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Banco do Brasil',
            ],
            [
                'country'        => $brasil,
                'bank_name'      => 'Itaú Unibanco',
                'account_number' => '654321-0',
                'account_type'   => 'corriente',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Itaú BR',
            ],
        ];

        // Chile y Colombia — cuentas faltantes
        $chile    = Country::where('code_iso', 'CL')->first();
        $colombia = Country::where('code_iso', 'CO')->first();

        if ($chile) {
            $businessAccounts[] = [
                'country'        => $chile,
                'bank_name'      => 'Banco de Chile',
                'account_number' => '00012345678',
                'account_type'   => 'ahorro',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Banco de Chile',
            ];
            $businessAccounts[] = [
                'country'        => $chile,
                'bank_name'      => 'Banco Estado',
                'account_number' => '98765432100',
                'account_type'   => 'corriente',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Banco Estado CL',
            ];
        }

        if ($colombia) {
            $businessAccounts[] = [
                'country'        => $colombia,
                'bank_name'      => 'Bancolombia',
                'account_number' => '12345678901',
                'account_type'   => 'ahorro',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Bancolombia',
            ];
            $businessAccounts[] = [
                'country'        => $colombia,
                'bank_name'      => 'Davivienda',
                'account_number' => '09876543210',
                'account_type'   => 'corriente',
                'account_holder' => 'Cambio J SAC',
                'alias'          => 'Cambio J - Davivienda CO',
            ];
        }

        foreach ($businessAccounts as $acctData) {
            $country = $acctData['country'];
            $bank    = Bank::where('country_id', $country->id)
                           ->where('name', $acctData['bank_name'])
                           ->first();

            if (!$bank) {
                $this->command->warn("⚠️  Banco no encontrado: {$acctData['bank_name']} ({$country->name}) — saltando");
                continue;
            }

            $account = BusinessAccount::firstOrCreate(
                [
                    'country_id'     => $country->id,
                    'bank_id'        => $bank->id,
                    'account_number' => $acctData['account_number'],
                ],
                [
                    'alias'          => $acctData['alias'],
                    'account_type'   => $acctData['account_type'],
                    'account_holder' => $acctData['account_holder'],
                    'dni_ruc'        => null,
                    'active'         => true,
                ]
            );

            // Asignar a todos los sellers
            foreach ($sellers as $seller) {
                if (!$account->sellers()->where('seller_id', $seller->id)->exists()) {
                    $account->sellers()->attach($seller->id);
                }
            }

            $this->command->info("✅ Cuenta {$acctData['alias']} creada y asignada a " . $sellers->count() . " vendedor(es)");
        }

        // ─── NUEVOS PARES DE DIVISAS ──────────────────────────────────────────
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();
        $cop = Currency::where('code', 'COP')->first();
        $ars = Currency::where('code', 'ARS')->first();
        $brl = Currency::where('code', 'BRL')->first();

        $newPairs = [
            ['from' => $cop, 'to' => $pen, 'ves_rate' => 0.00082, 'usd_rate' => 0.00025, 'eur_rate' => 0.00027],
            ['from' => $ars, 'to' => $pen, 'ves_rate' => 0.0031,  'usd_rate' => 0.00083, 'eur_rate' => 0.00091],
            ['from' => $brl, 'to' => $pen, 'ves_rate' => 0.72,    'usd_rate' => 0.195,   'eur_rate' => 0.212],
            ['from' => $brl, 'to' => $ves, 'ves_rate' => 86.20,   'usd_rate' => 23.50,   'eur_rate' => 25.50],
        ];

        foreach ($newPairs as $p) {
            if (!$p['from'] || !$p['to']) {
                $this->command->warn('⚠️  Moneda no encontrada, saltando par');
                continue;
            }

            $pair = CurrencyPair::firstOrCreate(
                ['from_currency_id' => $p['from']->id, 'to_currency_id' => $p['to']->id],
                ['is_active' => true]
            );

            if (!$pair->is_active) {
                $pair->update(['is_active' => true]);
            }

            $existing = ExchangeRate::where('currency_pair_id', $pair->id)
                ->where('is_active', true)
                ->first();

            if (!$existing) {
                ExchangeRate::create([
                    'currency_pair_id' => $pair->id,
                    'ves_rate'         => $p['ves_rate'],
                    'usd_rate'         => $p['usd_rate'],
                    'eur_rate'         => $p['eur_rate'],
                    'is_active'        => true,
                ]);
            }

            $this->command->info("✅ Par {$p['from']->code}→{$p['to']->code} activo");
        }

        $this->command->info('✅ MoreCorridorsSeeder completado: Argentina + Brasil + cuentas CL/CO/AR/BR + nuevos pares');
    }
}
