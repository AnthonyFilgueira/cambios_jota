<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $countries = Country::whereIn('code_iso', ['PE', 'VE', 'CL', 'CO', 'AR', 'BR'])
            ->get()
            ->keyBy('code_iso');

        $methods = [
            // ─── PERÚ (sender/recipient — es tanto origen como destino) ────
            'PE' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'both',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
                [
                    'code'            => 'agente',
                    'name'            => 'Agente',
                    'side'            => 'sender',
                    'fields_required' => ['bank'],
                ],
                [
                    'code'            => 'yape',
                    'name'            => 'Yape',
                    'side'            => 'both',
                    'fields_required' => ['phone'],
                ],
                [
                    'code'            => 'plin',
                    'name'            => 'Plin',
                    'side'            => 'both',
                    'fields_required' => ['phone'],
                ],
            ],

            // ─── VENEZUELA (recipient side) ───────────────────────────────
            'VE' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'recipient',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
                [
                    'code'            => 'pago_movil',
                    'name'            => 'Pago Móvil',
                    'side'            => 'recipient',
                    'fields_required' => ['phone', 'bank'],
                ],
            ],

            // ─── CHILE (sender side) ──────────────────────────────────────
            'CL' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
            ],

            // ─── COLOMBIA (sender side) ───────────────────────────────────
            'CO' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
                [
                    'code'            => 'nequi',
                    'name'            => 'Nequi',
                    'side'            => 'sender',
                    'fields_required' => ['phone'],
                ],
                [
                    'code'            => 'daviplata',
                    'name'            => 'Daviplata',
                    'side'            => 'sender',
                    'fields_required' => ['phone'],
                ],
            ],

            // ─── ARGENTINA (sender side) ──────────────────────────────────
            'AR' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
                [
                    'code'            => 'cvu_alias',
                    'name'            => 'CVU / Alias',
                    'side'            => 'sender',
                    'fields_required' => ['account_number'],
                ],
            ],

            // ─── BRASIL (sender side) ─────────────────────────────────────
            'BR' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
                ],
                [
                    'code'            => 'pix',
                    'name'            => 'PIX',
                    'side'            => 'sender',
                    'fields_required' => ['phone'],
                ],
            ],
        ];

        foreach ($methods as $iso => $countryMethods) {
            $country = $countries->get($iso);

            if (! $country) {
                continue;
            }

            foreach ($countryMethods as $method) {
                PaymentMethod::updateOrCreate(
                    ['country_id' => $country->id, 'code' => $method['code']],
                    [
                        'name'            => $method['name'],
                        'side'            => $method['side'],
                        'fields_required' => $method['fields_required'],
                        'active'          => true,
                    ]
                );
            }
        }
    }
}
