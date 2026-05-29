<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗑️  Limpiando transacciones y ventas...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('transaction_incentive_rules')->truncate();
        DB::table('transaction_logs')->truncate();
        DB::table('transactions')->truncate();
        DB::table('sale_logs')->truncate();
        DB::table('wallet_transactions')->truncate();
        DB::table('sales')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✅ Sistema limpio. Transacciones, ventas y monedero eliminados. Listo para pruebas desde cero.');
    }
}
