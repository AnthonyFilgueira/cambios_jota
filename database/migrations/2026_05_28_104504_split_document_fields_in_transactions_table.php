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
            // Sender (Perú) — add new split fields, keep sender_dni for backward compat
            $table->string('sender_document_type', 20)->nullable()->after('sender_dni');
            $table->string('sender_document_number', 50)->nullable()->after('sender_document_type');

            // Recipient (Venezuela) — add new split fields, keep recipient_dni for backward compat
            $table->string('recipient_document_type', 20)->nullable()->after('recipient_dni');
            $table->string('recipient_document_number', 50)->nullable()->after('recipient_document_type');
        });

        // Migrate existing data: copy old dni fields into new number fields
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE transactions SET sender_document_number = sender_dni WHERE sender_dni IS NOT NULL AND sender_document_number IS NULL"
        );
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE transactions SET recipient_document_number = recipient_dni WHERE recipient_dni IS NOT NULL AND recipient_document_number IS NULL"
        );
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'sender_document_type',
                'sender_document_number',
                'recipient_document_type',
                'recipient_document_number',
            ]);
        });
    }
};
