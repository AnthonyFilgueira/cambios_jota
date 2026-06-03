<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Delegador por defecto → CambiosJota
// Para otro cliente: php artisan db:seed --class=VipMoneyDatabaseSeeder
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([CambiosJotaDatabaseSeeder::class]);
    }
}
