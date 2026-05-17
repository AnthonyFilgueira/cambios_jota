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
            $table->enum('operation_type', ['transferencia', 'pago_movil'])->default('transferencia')->after('exchange_rate_id');
            $table->string('recipient_phone', 30)->nullable()->after('recipient_dni');
            $table->string('sender_dni', 30)->nullable()->after('sender_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['operation_type', 'recipient_phone', 'sender_dni']);
        });
    }
};
