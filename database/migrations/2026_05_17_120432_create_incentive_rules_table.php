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
        Schema::create('incentive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('description', 500)->nullable();
            $table->enum('type', ['bono_volumen', 'bono_estrella', 'descuento_cliente', 'bono_nuevo_cliente']);
            $table->enum('target_type', ['seller', 'client', 'all'])->default('all');
            $table->unsignedBigInteger('target_id')->nullable(); // null = aplica a todos del tipo
            $table->enum('value_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 8, 2); // % o monto fijo
            $table->decimal('min_amount', 10, 2)->nullable(); // Monto mínimo de transacción para activar
            $table->integer('min_transactions')->nullable(); // Mínimo de transacciones en el período
            $table->date('starts_at');
            $table->date('ends_at')->nullable(); // null = sin vencimiento
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['active', 'starts_at', 'ends_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentive_rules');
    }
};
