<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// VipMoney — solo usuario administrador; la configuración se agrega según requerimiento
class VipMoneyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 [VipMoney] Seeding database...');

        // 1. Roles y permisos
        $this->call([RolesAndPermissionsSeeder::class]);

        // 2. Administrador VipMoney
        $admin = User::updateOrCreate(
            ['email' => 'admin@vipmoney.com'],
            [
                'name'              => 'Admin VipMoney',
                'password'          => Hash::make('VipMoney2026!'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('super-admin');

        $this->command->info('✅ [VipMoney] Database seeding completed!');
    }
}
