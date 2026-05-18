<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Vaciar registros existentes (CRUD vacío, sin datos reales aún)
        DB::table('incentive_rules')->delete();

        DB::statement("ALTER TABLE incentive_rules MODIFY COLUMN type ENUM('extra_receptor','extra_comision','descuento_tasa') NOT NULL");

        DB::statement("ALTER TABLE incentive_rules MODIFY COLUMN target_type ENUM('todos_clientes','cliente_nuevo','cliente_especifico','todos_vendedores','vendedor_especifico','clientes_de_vendedor') NOT NULL DEFAULT 'todos_clientes'");

        Schema::table('incentive_rules', function (Blueprint $table) {
            $table->unsignedInteger('max_uses')->nullable()->after('ends_at');
            $table->unsignedInteger('uses_count')->default(0)->after('max_uses');
            $table->boolean('condition_new_client')->default(false)->after('uses_count');
        });
    }

    public function down(): void
    {
        Schema::table('incentive_rules', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'uses_count', 'condition_new_client']);
        });

        DB::statement("ALTER TABLE incentive_rules MODIFY COLUMN type ENUM('bono_volumen','bono_estrella','descuento_cliente','bono_nuevo_cliente') NOT NULL");
        DB::statement("ALTER TABLE incentive_rules MODIFY COLUMN target_type ENUM('seller','client','all') NOT NULL DEFAULT 'all'");
    }
};
