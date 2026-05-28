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
        $peru      = Country::where('code_iso', 'PE')->first();
        $venezuela = Country::where('code_iso', 'VE')->first();

        if ($peru) {
            PaymentMethod::firstOrCreate(
                ['country_id' => $peru->id, 'code' => 'transferencia_bancaria'],
                ['name' => 'Transferencia Bancaria', 'active' => true]
            );
        }

        if ($venezuela) {
            $veMethods = [
                ['code' => 'transferencia_bancaria', 'name' => 'Transferencia Bancaria'],
                ['code' => 'pago_movil',             'name' => 'Pago Móvil'],
            ];

            foreach ($veMethods as $method) {
                PaymentMethod::firstOrCreate(
                    ['country_id' => $venezuela->id, 'code' => $method['code']],
                    array_merge($method, ['active' => true])
                );
            }
        }
    }
}
