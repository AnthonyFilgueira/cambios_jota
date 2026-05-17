<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── MÓDULOS ─────────────────────────────────────────────────────
        $modules = [
            ['name' => 'Dashboard',          'slug' => 'dashboard',       'icon' => '🏠', 'order' => 1],
            ['name' => 'Transacciones',      'slug' => 'transactions',    'icon' => '💸', 'order' => 2],
            ['name' => 'Ventas',             'slug' => 'sales',           'icon' => '📋', 'order' => 3],
            ['name' => 'Vendedores',         'slug' => 'sellers',         'icon' => '👥', 'order' => 4],
            ['name' => 'Países y Cuentas',   'slug' => 'countries',       'icon' => '🌍', 'order' => 5],
            ['name' => 'Tasas de Cambio',    'slug' => 'exchange-rates',  'icon' => '💱', 'order' => 6],
            ['name' => 'Divisas y Pares',    'slug' => 'currencies',      'icon' => '🌐', 'order' => 7],
            ['name' => 'Liquidaciones',      'slug' => 'liquidations',    'icon' => '💰', 'order' => 8],
            ['name' => 'Reportes',           'slug' => 'reports',         'icon' => '📊', 'order' => 9],
            ['name' => 'Monedero',           'slug' => 'wallet',          'icon' => '👛', 'order' => 10],
            ['name' => 'Administración',     'slug' => 'admin',           'icon' => '⚙️',  'order' => 11],
        ];

        $moduleMap = [];
        foreach ($modules as $m) {
            $mod = Module::create($m);
            $moduleMap[$m['slug']] = $mod->id;
        }

        // ─── PERMISOS POR MÓDULO ─────────────────────────────────────────
        $permissions = [
            // Dashboard
            ['name' => 'view-owner-dashboard',   'label' => 'Ver dashboard admin',      'module' => 'dashboard'],
            ['name' => 'view-seller-dashboard',  'label' => 'Ver dashboard vendedor',   'module' => 'dashboard'],
            ['name' => 'view-client-dashboard',  'label' => 'Ver dashboard cliente',    'module' => 'dashboard'],

            // Transacciones
            ['name' => 'view-own-transactions',  'label' => 'Ver mis transacciones',    'module' => 'transactions'],
            ['name' => 'view-all-transactions',  'label' => 'Ver todas las transacciones', 'module' => 'transactions'],
            ['name' => 'create-transactions',    'label' => 'Crear transacción',        'module' => 'transactions'],
            ['name' => 'manage-transactions',    'label' => 'Gestionar transacciones',  'module' => 'transactions'],

            // Ventas
            ['name' => 'view-own-sales',         'label' => 'Ver mis ventas',           'module' => 'sales'],
            ['name' => 'view-sales',             'label' => 'Ver todas las ventas',     'module' => 'sales'],
            ['name' => 'create-sales',           'label' => 'Registrar venta',          'module' => 'sales'],
            ['name' => 'edit-sales',             'label' => 'Editar venta',             'module' => 'sales'],
            ['name' => 'approve-sales',          'label' => 'Aprobar venta',            'module' => 'sales'],
            ['name' => 'reject-sales',           'label' => 'Rechazar venta',           'module' => 'sales'],
            ['name' => 'observe-sales',          'label' => 'Observar venta',           'module' => 'sales'],

            // Vendedores
            ['name' => 'view-sellers',           'label' => 'Ver vendedores',           'module' => 'sellers'],
            ['name' => 'create-sellers',         'label' => 'Registrar vendedor',       'module' => 'sellers'],
            ['name' => 'edit-sellers',           'label' => 'Editar vendedor',          'module' => 'sellers'],
            ['name' => 'delete-sellers',         'label' => 'Desactivar vendedor',      'module' => 'sellers'],

            // Países y Cuentas
            ['name' => 'view-countries',         'label' => 'Ver países',               'module' => 'countries'],
            ['name' => 'manage-countries',       'label' => 'Gestionar países',         'module' => 'countries'],
            ['name' => 'manage-banks',           'label' => 'Gestionar bancos',         'module' => 'countries'],
            ['name' => 'manage-accounts',        'label' => 'Gestionar cuentas del negocio', 'module' => 'countries'],
            ['name' => 'assign-accounts',        'label' => 'Asignar cuentas a vendedores',  'module' => 'countries'],

            // Tasas de Cambio
            ['name' => 'view-exchange-rates',    'label' => 'Ver tasas de cambio',      'module' => 'exchange-rates'],
            ['name' => 'create-exchange-rates',  'label' => 'Registrar tasa',           'module' => 'exchange-rates'],
            ['name' => 'edit-exchange-rates',    'label' => 'Editar tasa',              'module' => 'exchange-rates'],
            ['name' => 'activate-exchange-rates','label' => 'Activar/desactivar tasa',  'module' => 'exchange-rates'],

            // Divisas y Pares
            ['name' => 'view-currencies',        'label' => 'Ver divisas',              'module' => 'currencies'],
            ['name' => 'create-currencies',      'label' => 'Registrar divisa',         'module' => 'currencies'],
            ['name' => 'edit-currencies',        'label' => 'Editar divisa',            'module' => 'currencies'],
            ['name' => 'view-currency-pairs',    'label' => 'Ver pares de divisas',     'module' => 'currencies'],
            ['name' => 'create-currency-pairs',  'label' => 'Registrar par de divisas', 'module' => 'currencies'],
            ['name' => 'edit-currency-pairs',    'label' => 'Editar par de divisas',    'module' => 'currencies'],
            ['name' => 'view-corridors',         'label' => 'Ver corredores',           'module' => 'currencies'],
            ['name' => 'create-corridors',       'label' => 'Registrar corredor',       'module' => 'currencies'],
            ['name' => 'edit-corridors',         'label' => 'Editar corredor',          'module' => 'currencies'],
            ['name' => 'view-corridor-matrix',   'label' => 'Ver matriz de corredores', 'module' => 'currencies'],
            ['name' => 'edit-corridor-matrix',   'label' => 'Editar matriz de corredores', 'module' => 'currencies'],

            // Liquidaciones
            ['name' => 'view-liquidations',      'label' => 'Ver liquidaciones',        'module' => 'liquidations'],
            ['name' => 'create-liquidations',    'label' => 'Registrar liquidación',    'module' => 'liquidations'],
            ['name' => 'edit-liquidations',      'label' => 'Editar liquidación',       'module' => 'liquidations'],

            // Reportes
            ['name' => 'view-reports',           'label' => 'Ver reportes',             'module' => 'reports'],
            ['name' => 'view-rankings',          'label' => 'Ver rankings',             'module' => 'reports'],
            ['name' => 'export-reports',         'label' => 'Exportar reportes',        'module' => 'reports'],
            ['name' => 'view-seller-performance','label' => 'Ver rendimiento por vendedor', 'module' => 'reports'],

            // Monedero
            ['name' => 'view-own-wallet',        'label' => 'Ver mi monedero',          'module' => 'wallet'],
            ['name' => 'view-all-wallets',       'label' => 'Ver todos los monederos',  'module' => 'wallet'],

            // Administración
            ['name' => 'manage-roles',           'label' => 'Gestionar roles y permisos', 'module' => 'admin'],
            ['name' => 'view-audit-log',         'label' => 'Ver log de auditoría',     'module' => 'admin'],
        ];

        $createdPerms = [];
        foreach ($permissions as $p) {
            $perm = Permission::create([
                'name'      => $p['name'],
                'label'     => $p['label'],
                'module_id' => $moduleMap[$p['module']],
            ]);
            $createdPerms[$p['name']] = $perm;
        }

        // ─── ROLES ───────────────────────────────────────────────────────

        // super-admin: acceso total
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // admin (dueño): todo excepto gestionar roles
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(array_filter(array_keys($createdPerms), fn($n) => $n !== 'manage-roles'));

        // contador: solo lectura de finanzas
        $contador = Role::create(['name' => 'contador']);
        $contador->givePermissionTo([
            'view-owner-dashboard',
            'view-sales', 'view-own-sales',
            'view-sellers',
            'view-reports', 'view-rankings', 'export-reports', 'view-seller-performance',
            'view-exchange-rates',
            'view-liquidations',
            'view-all-transactions',
            'view-all-wallets',
            'view-countries',
        ]);

        // vendedor: gestión de sus ventas y comisiones
        $vendedor = Role::create(['name' => 'vendedor']);
        $vendedor->givePermissionTo([
            'view-seller-dashboard',
            'view-own-sales', 'create-sales', 'edit-sales',
            'approve-sales', 'reject-sales', 'observe-sales',
            'view-own-transactions',
            'view-own-wallet',
            'view-exchange-rates',
        ]);

        // cliente: solo sus transacciones
        $cliente = Role::create(['name' => 'cliente']);
        $cliente->givePermissionTo([
            'view-client-dashboard',
            'view-own-transactions',
            'create-transactions',
        ]);

        $this->command->info('✅ Roles y permisos creados con módulos');
        $this->command->info('📦 Módulos: ' . count($modules));
        $this->command->info('🔑 Permisos: ' . count($permissions));
        $this->command->info('👤 Roles: super-admin, admin, contador, vendedor, cliente');
    }
}
