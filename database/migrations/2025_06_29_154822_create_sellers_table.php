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
       Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('seller_commission', 5, 2); // Ej: 10.50%
            $table->decimal('boss_commission', 5, 2);   // Ej: 5.00%
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
