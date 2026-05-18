<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_incentive_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('incentive_rule_id')->constrained()->onDelete('cascade');
            $table->decimal('bonus_amount', 10, 2);
            $table->enum('benefit_type', ['receptor', 'comision']);
            $table->timestamp('applied_at')->useCurrent();

            $table->index('transaction_id');
            $table->index('incentive_rule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_incentive_rules');
    }
};
