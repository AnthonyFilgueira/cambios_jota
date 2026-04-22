<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tasas BCV para historicidad (snapshot al momento de la transacción)
            $table->decimal('usd_bcv_rate', 15, 6)->nullable()->after('sender_account_number');
            $table->decimal('eur_bcv_rate', 15, 6)->nullable()->after('usd_bcv_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'usd_bcv_rate',
                'eur_bcv_rate',
            ]);
        });
    }
};
