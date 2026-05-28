<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\Country;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $countries = Country::whereIn('code_iso', ['PE', 'VE', 'CL', 'CO', 'AR', 'BR'])->get();

        $types = [
            ['code' => 'ahorro',    'name' => 'Cuenta de Ahorros'],
            ['code' => 'corriente', 'name' => 'Cuenta Corriente'],
        ];

        foreach ($countries as $country) {
            foreach ($types as $type) {
                AccountType::updateOrCreate(
                    ['country_id' => $country->id, 'code' => $type['code']],
                    ['name' => $type['name'], 'active' => true]
                );
            }
        }
    }
}
