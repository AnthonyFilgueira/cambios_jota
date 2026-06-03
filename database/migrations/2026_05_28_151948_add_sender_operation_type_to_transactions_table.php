<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('sender_operation_type', 50)->nullable()->after('operation_type');
            $table->string('sender_phone', 30)->nullable()->after('sender_account_number');
            $table->string('recipient_name', 150)->nullable()->after('recipient_phone');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('recipient_account_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['sender_operation_type', 'sender_phone', 'recipient_name']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('recipient_account_type', ['ahorro', 'corriente'])->nullable()->change();
        });
    }
};
