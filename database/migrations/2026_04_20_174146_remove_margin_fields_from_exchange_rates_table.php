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
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Primero eliminar foreign key constraint
            $table->dropForeign(['last_updated_by']);

            // Luego eliminar campos de margen que no se usan
            $table->dropColumn([
                'base_rate',
                'margin_type',
                'margin_value',
                'final_rate',
                'last_updated_by',
                'notes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            // Restaurar campos si se hace rollback
            $table->foreignId('currency_pair_id')->nullable()->constrained();
            $table->decimal('base_rate', 10, 5)->nullable();
            $table->enum('margin_type', ['none', 'percentage', 'fixed'])->default('none');
            $table->decimal('margin_value', 5, 2)->default(0);
            $table->decimal('final_rate', 10, 5)->nullable();
            $table->foreignId('last_updated_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
        });
    }
};
