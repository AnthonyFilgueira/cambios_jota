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
            // Comprobante de transferencia
            $table->string('voucher')->nullable()->after('notes');

            // Datos bancarios del CLIENTE (para recibir dinero en Venezuela)
            $table->string('recipient_bank')->nullable()->after('voucher');
            $table->string('recipient_account_number')->nullable()->after('recipient_bank');
            $table->string('recipient_dni')->nullable()->after('recipient_account_number');
            $table->enum('recipient_account_type', ['ahorro', 'corriente'])->nullable()->after('recipient_dni');

            // Datos de transferencia del cliente (banco desde donde transfirió en Perú)
            $table->string('sender_bank')->nullable()->after('recipient_account_type');
            $table->string('sender_account_number')->nullable()->after('sender_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'voucher',
                'recipient_bank',
                'recipient_account_number',
                'recipient_dni',
                'recipient_account_type',
                'sender_bank',
                'sender_account_number',
            ]);
        });
    }
};
