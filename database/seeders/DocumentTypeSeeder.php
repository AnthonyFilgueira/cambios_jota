<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $peru      = Country::where('code_iso', 'PE')->first();
        $venezuela = Country::where('code_iso', 'VE')->first();

        if ($peru) {
            $peruTypes = [
                ['code' => 'DNI', 'name' => 'Documento Nacional de Identidad', 'prefix' => null,  'placeholder' => '12345678'],
                ['code' => 'CE',  'name' => 'Carné de Extranjería',            'prefix' => null,  'placeholder' => '000123456'],
                ['code' => 'RUC', 'name' => 'Registro Único de Contribuyentes','prefix' => null,  'placeholder' => '20123456789'],
            ];

            foreach ($peruTypes as $type) {
                DocumentType::firstOrCreate(
                    ['country_id' => $peru->id, 'code' => $type['code']],
                    array_merge($type, ['country_id' => $peru->id, 'active' => true])
                );
            }
        }

        if ($venezuela) {
            $veTypes = [
                ['code' => 'V', 'name' => 'Cédula venezolana',          'prefix' => 'V-', 'placeholder' => 'V-12345678'],
                ['code' => 'E', 'name' => 'Cédula extranjera',          'prefix' => 'E-', 'placeholder' => 'E-12345678'],
                ['code' => 'J', 'name' => 'RIF Jurídico',               'prefix' => 'J-', 'placeholder' => 'J-12345678-9'],
                ['code' => 'G', 'name' => 'RIF Gubernamental',          'prefix' => 'G-', 'placeholder' => 'G-12345678-9'],
            ];

            foreach ($veTypes as $type) {
                DocumentType::firstOrCreate(
                    ['country_id' => $venezuela->id, 'code' => $type['code']],
                    array_merge($type, ['country_id' => $venezuela->id, 'active' => true])
                );
            }
        }
    }
}
