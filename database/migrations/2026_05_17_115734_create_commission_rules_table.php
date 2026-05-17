<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('seller_value', 8, 2)->default(0); // % o monto fijo para el vendedor
            $table->decimal('boss_value', 8, 2)->default(0);   // % o monto fijo para el jefe
            $table->string('notes', 500)->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
