<?php

namespace Database\Seeders;

use App\Models\IncentiveRule;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class IncentiveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎁 Creando incentivos de demostración...');

        $admin     = User::where('email', 'cambiosjottaa@innodite.com')->first();
        $adminId   = $admin?->id ?? 1;

        $juan      = User::where('email', 'juan.perez@gmail.com')->first();
        $pedro     = Seller::where('code', 'VEND001')->first();

        // ── 1. BONO LANZAMIENTO — todos los clientes, S/10 fijo, sin vencimiento
        IncentiveRule::create([
            'name'                 => 'Bono Lanzamiento 🚀',
            'description'          => 'S/10 extra para todos los clientes durante el lanzamiento',
            'type'                 => 'extra_receptor',
            'target_type'          => 'todos_clientes',
            'value_type'           => 'fixed',
            'value'                => 10,
            'active'               => true,
            'starts_at'            => '2026-05-01',
            'ends_at'              => null,
            'max_uses'             => null,
            'uses_count'           => 0,
            'condition_new_client' => false,
            'created_by'           => $adminId,
        ]);

        // ── 2. BONO PRIMER ENVÍO — solo clientes nuevos, 5%, máx 50 usos, vence 31 mayo
        IncentiveRule::create([
            'name'                 => 'Bono Primer Envío',
            'description'          => '5% extra solo para tu primer envío. ¡Aprovéchalo!',
            'type'                 => 'extra_receptor',
            'target_type'          => 'todos_clientes',
            'value_type'           => 'percentage',
            'value'                => 5,
            'active'               => true,
            'starts_at'            => '2026-05-01',
            'ends_at'              => '2026-05-31',
            'max_uses'             => 50,
            'uses_count'           => 23,
            'condition_new_client' => true,
            'created_by'           => $adminId,
        ]);

        // ── 3. EXTRA COMISIÓN MAYO — todos los vendedores, 2%, vence 31 mayo
        IncentiveRule::create([
            'name'                 => 'Comisión Extra Mayo',
            'description'          => '2% de comisión extra por cada transacción completada en mayo',
            'type'                 => 'extra_comision',
            'target_type'          => 'todos_vendedores',
            'value_type'           => 'percentage',
            'value'                => 2,
            'active'               => true,
            'starts_at'            => '2026-05-01',
            'ends_at'              => '2026-05-31',
            'max_uses'             => null,
            'uses_count'           => 8,
            'condition_new_client' => false,
            'created_by'           => $adminId,
        ]);

        // ── 4. BONO VIP — cliente específico Juan Pérez, S/20 fijo, max 5 usos
        if ($juan) {
            IncentiveRule::create([
                'name'                 => 'Bono VIP Juan',
                'description'          => 'Beneficio especial para cliente Juan Pérez',
                'type'                 => 'extra_receptor',
                'target_type'          => 'cliente_especifico',
                'target_id'            => $juan->id,
                'value_type'           => 'fixed',
                'value'                => 20,
                'active'               => true,
                'starts_at'            => '2026-05-01',
                'ends_at'              => null,
                'max_uses'             => 5,
                'uses_count'           => 1,
                'condition_new_client' => false,
                'created_by'           => $adminId,
            ]);
        }

        // ── 5. BONO CLIENTES DE PEDRO — clientes de VEND001, S/8 fijo
        if ($pedro) {
            IncentiveRule::create([
                'name'                 => 'Bono Clientes Pedro',
                'description'          => 'Bono exclusivo para clientes del vendedor Pedro Martínez',
                'type'                 => 'extra_receptor',
                'target_type'          => 'clientes_de_vendedor',
                'target_id'            => $pedro->id,
                'value_type'           => 'fixed',
                'value'                => 8,
                'active'               => true,
                'starts_at'            => '2026-05-01',
                'ends_at'              => null,
                'max_uses'             => null,
                'uses_count'           => 0,
                'condition_new_client' => false,
                'created_by'           => $adminId,
            ]);
        }

        // ── 6. PROMO ABRIL (vencida) — para poblar lista de inactivos
        IncentiveRule::create([
            'name'                 => 'Promo Abril 2026',
            'description'          => 'Campaña del mes de abril — finalizada',
            'type'                 => 'extra_receptor',
            'target_type'          => 'todos_clientes',
            'value_type'           => 'fixed',
            'value'                => 15,
            'active'               => true,
            'starts_at'            => '2026-04-01',
            'ends_at'              => '2026-04-30',
            'max_uses'             => null,
            'uses_count'           => 47,
            'condition_new_client' => false,
            'created_by'           => $adminId,
        ]);

        // ── 7. BLACK FRIDAY (usos agotados) — para poblar lista de inactivos
        IncentiveRule::create([
            'name'                 => 'Black Friday Especial',
            'description'          => 'Cupo de 100 usos agotado',
            'type'                 => 'extra_receptor',
            'target_type'          => 'todos_clientes',
            'value_type'           => 'percentage',
            'value'                => 10,
            'active'               => true,
            'starts_at'            => '2026-05-01',
            'ends_at'              => '2026-12-31',
            'max_uses'             => 100,
            'uses_count'           => 100,
            'condition_new_client' => false,
            'created_by'           => $adminId,
        ]);

        $activos = IncentiveRule::active()->count();
        $total   = IncentiveRule::count();

        $this->command->info("  ✓ $total incentivos creados ($activos activos, " . ($total - $activos) . ' inactivos/vencidos)');
    }
}
