<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes o crear uno de prueba
        $users = User::all();

        if ($users->isEmpty()) {
            $user = User::factory()->create([
                'name' => 'Usuario de Prueba',
                'email' => 'test@cambiosj.com',
            ]);
            $users = collect([$user]);
        }

        // Crear 15 transacciones distribuidas entre los usuarios
        foreach ($users as $user) {
            Transaction::factory()
                ->count(15)
                ->create([
                    'user_id' => $user->id
                ]);
        }

        $this->command->info('15 transacciones creadas para cada usuario.');
    }
}
