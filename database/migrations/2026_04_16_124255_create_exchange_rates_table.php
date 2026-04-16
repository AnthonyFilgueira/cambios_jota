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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('usd_rate', 10, 5)->comment('Tasa BCV del dólar');
            $table->decimal('eur_rate', 10, 5)->comment('Tasa BCV del euro');
            $table->decimal('ves_rate', 10, 5)->comment('Tasa propia VES/PEN');
            $table->boolean('is_active')->default(true)->comment('Solo una tasa puede estar activa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
