<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyPair;
use App\Models\DocumentType;
use App\Models\ExchangeRate;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class MultiCorridorSeeder extends Seeder
{
    public function run(): void
    {
        // ─── PAÍSES NUEVOS ───────────────────────────────────────────────────

        $chile = Country::firstOrCreate(
            ['code_iso' => 'CL'],
            [
                'name'          => 'Chile',
                'emoji'         => '🇨🇱',
                'currency_name' => 'CLP — Peso chileno',
                'role'          => 'origin',
                'active'        => true,
            ]
        );

        $colombia = Country::firstOrCreate(
            ['code_iso' => 'CO'],
            [
                'name'          => 'Colombia',
                'emoji'         => '🇨🇴',
                'currency_name' => 'COP — Peso colombiano',
                'role'          => 'origin',
                'active'        => true,
            ]
        );

        // ─── VINCULAR MONEDAS A PAÍSES ───────────────────────────────────────
        Currency::where('code', 'CLP')->whereNull('country_id')->update(['country_id' => $chile->id]);
        Currency::where('code', 'COP')->whereNull('country_id')->update(['country_id' => $colombia->id]);

        // ─── BANCOS CHILE ─────────────────────────────────────────────────────
        $bancosCl = [
            ['name' => 'Banco de Chile',       'swift_code' => 'BCHICLRM'],
            ['name' => 'Banco Estado',         'swift_code' => 'BECHCLRM'],
            ['name' => 'Santander Chile',      'swift_code' => 'BSCHCLRM'],
            ['name' => 'BCI',                  'swift_code' => 'CREDCLRM'],
            ['name' => 'Banco Falabella Chile','swift_code' => null],
            ['name' => 'Banco Itaú Chile',     'swift_code' => 'ITAUCLRM'],
            ['name' => 'BBVA Chile',           'swift_code' => 'BSCHCLRM'],
        ];

        foreach ($bancosCl as $b) {
            Bank::firstOrCreate(
                ['country_id' => $chile->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // ─── BANCOS COLOMBIA ──────────────────────────────────────────────────
        $bancoCo = [
            ['name' => 'Bancolombia',              'swift_code' => 'COLOCOBM'],
            ['name' => 'Banco de Bogotá',          'swift_code' => 'BDBOCOBB'],
            ['name' => 'Davivienda',               'swift_code' => 'CAFICOBB'],
            ['name' => 'BBVA Colombia',            'swift_code' => 'BSCHCOBB'],
            ['name' => 'Banco Popular',            'swift_code' => null],
            ['name' => 'Nequi / Bancolombia',      'swift_code' => null],
            ['name' => 'Daviplata / Davivienda',   'swift_code' => null],
        ];

        foreach ($bancoCo as $b) {
            Bank::firstOrCreate(
                ['country_id' => $colombia->id, 'name' => $b['name']],
                ['swift_code' => $b['swift_code'], 'active' => true]
            );
        }

        // ─── TIPOS DE DOCUMENTO CHILE ─────────────────────────────────────────
        $docsCl = [
            ['code' => 'RUT', 'name' => 'Rol Único Tributario', 'prefix' => null, 'placeholder' => '12.345.678-9'],
            ['code' => 'RUN', 'name' => 'Rol Único Nacional',   'prefix' => null, 'placeholder' => '12.345.678-9'],
        ];

        foreach ($docsCl as $d) {
            DocumentType::firstOrCreate(
                ['country_id' => $chile->id, 'code' => $d['code']],
                array_merge($d, ['country_id' => $chile->id, 'active' => true])
            );
        }

        // ─── TIPOS DE DOCUMENTO COLOMBIA ─────────────────────────────────────
        $docsCo = [
            ['code' => 'CC',  'name' => 'Cédula de Ciudadanía', 'prefix' => null, 'placeholder' => '1234567890'],
            ['code' => 'CE',  'name' => 'Cédula de Extranjería','prefix' => null, 'placeholder' => '123456789'],
            ['code' => 'NIT', 'name' => 'NIT Empresarial',      'prefix' => null, 'placeholder' => '900123456-1'],
        ];

        foreach ($docsCo as $d) {
            DocumentType::firstOrCreate(
                ['country_id' => $colombia->id, 'code' => $d['code']],
                array_merge($d, ['country_id' => $colombia->id, 'active' => true])
            );
        }

        // ─── MÉTODOS DE PAGO CHILE ────────────────────────────────────────────
        PaymentMethod::firstOrCreate(
            ['country_id' => $chile->id, 'code' => 'transferencia_bancaria'],
            ['name' => 'Transferencia Bancaria', 'active' => true]
        );

        // ─── MÉTODOS DE PAGO COLOMBIA ─────────────────────────────────────────
        $methodsCo = [
            ['code' => 'transferencia_bancaria', 'name' => 'Transferencia Bancaria'],
            ['code' => 'nequi',                  'name' => 'Nequi'],
            ['code' => 'daviplata',              'name' => 'Daviplata'],
        ];

        foreach ($methodsCo as $m) {
            PaymentMethod::firstOrCreate(
                ['country_id' => $colombia->id, 'code' => $m['code']],
                ['name' => $m['name'], 'active' => true]
            );
        }

        // ─── NUEVOS PARES DE DIVISAS Y TASAS ─────────────────────────────────
        $pen = Currency::where('code', 'PEN')->first();
        $ves = Currency::where('code', 'VES')->first();
        $clp = Currency::where('code', 'CLP')->first();
        $cop = Currency::where('code', 'COP')->first();
        $usd = Currency::where('code', 'USD')->first();

        $usdBcv = 479.78;
        $eurBcv = 565.98;

        $newPairs = [
            // CLP → PEN (Chile envía a Perú)
            ['from' => $clp, 'to' => $pen, 'ves_rate' => 0.0045, 'usd_rate' => 0.001,  'eur_rate' => 0.0011],
            // CLP → VES (Chile envía a Venezuela)
            ['from' => $clp, 'to' => $ves, 'ves_rate' => 0.55,   'usd_rate' => 0.001,  'eur_rate' => 0.0011],
            // COP → VES (Colombia envía a Venezuela)
            ['from' => $cop, 'to' => $ves, 'ves_rate' => 0.12,   'usd_rate' => 0.0025, 'eur_rate' => 0.0028],
            // USD → PEN (USD a Perú)
            ['from' => $usd, 'to' => $pen, 'ves_rate' => 3.72,   'usd_rate' => 1.0,    'eur_rate' => 0.92],
        ];

        foreach ($newPairs as $p) {
            if (!$p['from'] || !$p['to']) continue;

            $pair = CurrencyPair::firstOrCreate(
                ['from_currency_id' => $p['from']->id, 'to_currency_id' => $p['to']->id],
                ['is_active' => true]
            );

            // Activar el par si estaba inactivo
            if (!$pair->is_active) {
                $pair->update(['is_active' => true]);
            }

            // Solo crear tasa si no hay una activa para este par
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

        // Activar par ARS → VES si existe inactivo
        $ars = Currency::where('code', 'ARS')->first();
        if ($ars && $ves) {
            $arsPair = CurrencyPair::where('from_currency_id', $ars->id)
                ->where('to_currency_id', $ves->id)
                ->first();
            if ($arsPair && !$arsPair->is_active) {
                $arsPair->update(['is_active' => true]);
            }
            $arsRate = ExchangeRate::where('currency_pair_id', $arsPair?->id)
                ->where('is_active', false)
                ->first();
            if ($arsRate) {
                $arsRate->update(['is_active' => true]);
                $this->command->info('✅ Par ARS→VES reactivado');
            }
        }

        $this->command->info('✅ MultiCorridorSeeder completado: Chile + Colombia + nuevos pares activos');
    }
}
