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
            // ─── PERÚ (sender side) ───────────────────────────────────────
            'PE' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
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
                    'side'            => 'sender',
                    'fields_required' => ['phone'],
                ],
                [
                    'code'            => 'plin',
                    'name'            => 'Plin',
                    'side'            => 'sender',
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
            ],

            // ─── ARGENTINA (sender side) ──────────────────────────────────
            'AR' => [
                [
                    'code'            => 'transferencia_bancaria',
                    'name'            => 'Transferencia Bancaria',
                    'side'            => 'sender',
                    'fields_required' => ['bank', 'account_number', 'account_type'],
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
