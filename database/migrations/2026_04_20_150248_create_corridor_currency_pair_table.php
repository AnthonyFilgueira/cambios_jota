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
        Schema::create('corridor_currency_pair', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corridor_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_pair_id')->constrained()->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Un corredor solo puede estar asignado una vez por par
            $table->unique(['corridor_id', 'currency_pair_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corridor_currency_pair');
    }
};
