<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar enum approval_status para agregar 'observed' y 'completed'
        DB::statement("ALTER TABLE sales MODIFY COLUMN approval_status ENUM('pending_seller', 'pending_admin', 'approved', 'rejected', 'observed', 'completed') NOT NULL DEFAULT 'pending_seller'");

        Schema::table('sales', function (Blueprint $table) {
            $table->string('voucher_path')->nullable()->after('approval_status');
            $table->text('admin_observation')->nullable()->after('voucher_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['voucher_path', 'admin_observation']);
        });

        // Revertir enum a valores originales
        DB::statement("ALTER TABLE sales MODIFY COLUMN approval_status ENUM('pending_seller', 'pending_admin', 'approved', 'rejected') NOT NULL DEFAULT 'pending_seller'");
    }
};
