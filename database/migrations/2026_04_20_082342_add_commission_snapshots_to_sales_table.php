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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('seller_commission_percent', 5, 2)->nullable()->after('approval_status');
            $table->decimal('admin_commission_percent', 5, 2)->nullable()->after('seller_commission_percent');
            $table->decimal('seller_commission_amount', 10, 2)->nullable()->comment('En SOLES')->after('admin_commission_percent');
            $table->decimal('admin_commission_amount', 10, 2)->nullable()->comment('En SOLES')->after('seller_commission_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'seller_commission_percent',
                'admin_commission_percent',
                'seller_commission_amount',
                'admin_commission_amount',
            ]);
        });
    }
};
