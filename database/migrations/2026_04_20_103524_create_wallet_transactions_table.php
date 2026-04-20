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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['commission', 'liquidation', 'adjustment']);
            $table->decimal('amount', 10, 2); // Puede ser positivo o negativo
            $table->decimal('balance_after', 10, 2); // Saldo después de esta transacción
            $table->text('description');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable(); // Sale, Liquidation, etc.
            $table->timestamps();

            // Índices para performance
            $table->index('seller_id');
            $table->index('created_at');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
