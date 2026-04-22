<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // CREAR PERMISOS POR MÓDULO
        // ========================================

        $permissions = [
            // Dashboard
            'view-owner-dashboard',
            'view-seller-dashboard',
            'view-client-dashboard',

            // Ventas
            'view-sales',
            'view-own-sales',
            'create-sales',
            'edit-sales',
            'approve-sales',
            'reject-sales',
            'observe-sales',

            // Vendedores
            'view-sellers',
            'create-sellers',
            'edit-sellers',
            'delete-sellers',

            // Reportes
            'view-reports',
            'view-rankings',
            'export-reports',
            'view-seller-performance',

            // Tasas de Cambio
            'view-exchange-rates',
            'create-exchange-rates',
            'edit-exchange-rates',
            'activate-exchange-rates',

            // Divisas
            'view-currencies',
            'create-currencies',
            'edit-currencies',

            // Pares de Divisas
            'view-currency-pairs',
            'create-currency-pairs',
            'edit-currency-pairs',

            // Corredores
            'view-corridors',
            'create-corridors',
            'edit-corridors',

            // Matriz de Corredores
            'view-corridor-matrix',
            'edit-corridor-matrix',

            // Liquidaciones
            'view-liquidations',
            'create-liquidations',
            'edit-liquidations',

            // Transacciones
            'view-own-transactions',
            'view-all-transactions',

            // Monedero
            'view-own-wallet',
            'view-all-wallets',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ========================================
        // CREAR ROLES Y ASIGNAR PERMISOS
        // ========================================

        // 1. SUPER ADMIN - Acceso total
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. ADMIN - Gestiona tasas y aprueba ventas (80% acceso)
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view-owner-dashboard',
            'view-sales',
            'create-sales',
            'edit-sales',
            'approve-sales',
            'reject-sales',
            'observe-sales',
            'view-sellers',
            'view-reports',
            'view-rankings',
            'export-reports',
            'view-seller-performance',
            'view-exchange-rates',
            'create-exchange-rates',
            'edit-exchange-rates',
            'activate-exchange-rates',
            'view-currencies',
            'view-currency-pairs',
            'view-corridors',
            'view-corridor-matrix',
            'view-liquidations',
            'create-liquidations',
            'view-all-transactions',
            'view-all-wallets',
        ]);

        // 3. CONTADOR - Solo lectura de ventas y reportes (40% acceso)
        $contador = Role::create(['name' => 'contador']);
        $contador->givePermissionTo([
            'view-sales',
            'view-sellers',
            'view-reports',
            'view-rankings',
            'export-reports',
            'view-seller-performance',
            'view-exchange-rates',
            'view-liquidations',
            'view-all-transactions',
            'view-all-wallets',
        ]);

        // 4. VENDEDOR - Registra ventas, ve comisiones (30% acceso)
        $vendedor = Role::create(['name' => 'vendedor']);
        $vendedor->givePermissionTo([
            'view-seller-dashboard',
            'view-own-sales',
            'create-sales',
            'edit-sales',
            'view-own-transactions',
            'view-own-wallet',
            'view-exchange-rates',
        ]);

        // 5. CLIENTE - Ve su historial de transacciones (10% acceso)
        $cliente = Role::create(['name' => 'cliente']);
        $cliente->givePermissionTo([
            'view-client-dashboard',
            'view-own-transactions',
        ]);

        $this->command->info('✅ Roles y permisos creados exitosamente');
        $this->command->info('📋 Roles: super-admin, admin, contador, vendedor, cliente');
        $this->command->info('🔑 Permisos: ' . count($permissions) . ' permisos creados');
    }
}
