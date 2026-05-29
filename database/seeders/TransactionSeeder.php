<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗑️  Limpiando transacciones...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('transaction_incentive_rules')->truncate();
        DB::table('transaction_logs')->truncate();
        DB::table('transactions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✅ Transacciones eliminadas. La tabla está lista para pruebas desde cero.');
    }
}
