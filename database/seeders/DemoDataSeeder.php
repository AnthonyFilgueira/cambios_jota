<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Seller;
use App\Models\Sale;
use App\Models\SaleLog;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    private $admin;
    private $clientes = [];
    private $vendedores = [];

    /**
     * Seeder completo con 30 ventas de demostración
     * Basado en requirement-30-ventas.md
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeder de datos de demostración...');

        // 1. Crear usuarios
        $this->crearUsuarios();

        // 2. Crear vendedores
        $this->crearVendedores();

        // 3. Crear ventas (30 total)
        $this->crearVentas();

        // 4. Crear observaciones/logs
        $this->crearObservaciones();

        $this->command->info('✅ Seeder completado exitosamente!');
        $this->mostrarResumen();
    }

    private function crearUsuarios()
    {
        $this->command->info('👤 Creando usuarios...');

        // Admin
        $this->admin = User::create([
            'name' => 'Admin Cambio J',
            'email' => 'cambios_jotta@innodite.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Clientes
        $clientesData = [
            ['name' => 'Juan Pérez', 'email' => 'juan.perez@gmail.com'],
            ['name' => 'María González', 'email' => 'maria.gonzalez@gmail.com'],
            ['name' => 'Carlos Rodríguez', 'email' => 'carlos.rodriguez@gmail.com'],
            ['name' => 'Ana Torres', 'email' => 'ana.torres@gmail.com'],
        ];

        foreach ($clientesData as $data) {
            $this->clientes[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('  ✓ Admin + 4 clientes creados');
    }

    private function crearVendedores()
    {
        $this->command->info('🏅 Creando vendedores...');

        $vendedoresData = [
            [
                'code' => 'VEND001',
                'name' => 'Pedro Martínez',
                'seller_commission' => 5.0,
                'boss_commission' => 15.0,
            ],
            [
                'code' => 'VEND002',
                'name' => 'Ana López',
                'seller_commission' => 7.0,
                'boss_commission' => 13.0,
            ],
            [
                'code' => 'VEND003',
                'name' => 'Luis Torres',
                'seller_commission' => 10.0,
                'boss_commission' => 10.0,
            ],
            [
                'code' => 'VEND004',
                'name' => 'Rosa Fernández',
                'seller_commission' => 5.0,
                'boss_commission' => 15.0,
            ],
        ];

        foreach ($vendedoresData as $data) {
            $this->vendedores[$data['code']] = Seller::create($data);
        }

        $this->command->info('  ✓ 4 vendedores creados (VEND001-VEND004)');
    }

    private function crearVentas()
    {
        $this->command->info('💰 Creando 30 ventas...');

        // PENDING (4 ventas)
        $this->crearVenta(500, 'pending_seller', Carbon::now()->subHours(2), 'VEND001');
        $this->crearVenta(1000, 'pending_seller', Carbon::now()->subHours(1), 'VEND002');
        $this->crearVenta(300, 'pending_seller', Carbon::now()->subHours(3), 'VEND001');
        $this->crearVenta(750, 'pending_seller', Carbon::now()->subHours(4), 'VEND003');

        // APPROVED (6 ventas)
        $this->crearVenta(400, 'approved', Carbon::now()->subHours(10), 'VEND001', true);
        $this->crearVenta(2000, 'approved', Carbon::now()->subHours(8), 'VEND002', true);
        $this->crearVenta(200, 'approved', Carbon::now()->subHours(6), 'VEND003', true);
        $this->crearVenta(1500, 'approved', Carbon::now()->subDays(2), 'VEND001', true);
        $this->crearVenta(5000, 'approved', Carbon::now()->subDays(2), 'VEND002', true);
        $this->crearVenta(600, 'approved', Carbon::now()->subDays(3), 'VEND001', true);

        // COMPLETED (12 ventas)
        $this->crearVenta(1000, 'completed', Carbon::now()->subDays(4), 'VEND001', true, true);
        $this->crearVenta(750, 'completed', Carbon::now()->subDays(7), 'VEND001', true, true);
        $this->crearVenta(500, 'completed', Carbon::now()->subDays(8), 'VEND002', true, true);
        $this->crearVenta(2000, 'completed', Carbon::now()->subDays(3), 'VEND003', true, true);
        $this->crearVenta(300, 'completed', Carbon::now()->subDays(10), 'VEND001', true, true);
        $this->crearVenta(1200, 'completed', Carbon::now()->subDays(5), 'VEND002', true, true);
        $this->crearVenta(800, 'completed', Carbon::now()->subDays(6), 'VEND001', true, true);
        $this->crearVenta(450, 'completed', Carbon::now()->subDays(12), 'VEND003', true, true);
        $this->crearVenta(3000, 'completed', Carbon::now()->subDays(4), 'VEND002', true, true);
        $this->crearVenta(950, 'completed', Carbon::now()->subDays(9), 'VEND001', true, true);
        $this->crearVenta(1800, 'completed', Carbon::now()->subDays(11), 'VEND003', true, true);
        $this->crearVenta(650, 'completed', Carbon::now()->subDays(13), 'VEND001', true, true);

        // REJECTED (4 ventas)
        $this->crearVenta(100, 'rejected', Carbon::now()->subDay(), 'VEND002', false, false, 'Datos de destinatario incorrectos');
        $this->crearVenta(10000, 'rejected', Carbon::now()->subDays(2), 'VEND001', false, false, 'Monto sospechoso, verificar identidad del cliente');
        $this->crearVenta(250, 'rejected', Carbon::now()->subDays(4), 'VEND003', false, false, 'Comprobante de pago ilegible');
        $this->crearVenta(150, 'rejected', Carbon::now()->subDays(5), 'VEND002', false, false, 'Cliente solicitó cancelación');

        // OBSERVED (4 ventas)
        $this->crearVenta(400, 'observed', Carbon::now()->subHours(5), 'VEND001', false, false, 'Falta número de cuenta del destinatario');
        $this->crearVenta(600, 'observed', Carbon::now()->subHours(6), 'VEND002', false, false, 'Nombre del destinatario no coincide con cédula');
        $this->crearVenta(800, 'observed', Carbon::now()->subDay(), 'VEND003', false, false, 'Comprobante de identidad ilegible, reenviar');
        $this->crearVenta(550, 'observed', Carbon::now()->subDays(2), 'VEND001', false, false, 'Verificar monto con el cliente (discrepancia reportada)');

        $this->command->info('  ✓ 30 ventas creadas');
        $this->command->info('    - PENDING: 4');
        $this->command->info('    - APPROVED: 6');
        $this->command->info('    - COMPLETED: 12');
        $this->command->info('    - REJECTED: 4');
        $this->command->info('    - OBSERVED: 4');
    }

    private function crearVenta($monto, $estado, $fecha, $vendedorCode, $conSnapshots = false, $conComprobante = false, $observacion = null)
    {
        $vendedor = $this->vendedores[$vendedorCode];

        $data = [
            'seller_id' => $vendedor->id,
            'amount' => $monto,
            'sale_date' => $fecha,
            'approval_status' => $estado,
            'admin_observation' => $observacion,
        ];

        // Agregar snapshots de comisiones para ventas aprobadas/completadas
        if ($conSnapshots) {
            $data['seller_commission_percent'] = $vendedor->seller_commission;
            $data['admin_commission_percent'] = $vendedor->boss_commission;
            $data['seller_commission_amount'] = $monto * ($vendedor->seller_commission / 100);
            $data['admin_commission_amount'] = $monto * ($vendedor->boss_commission / 100);
        }

        // Agregar comprobante para ventas completadas
        if ($conComprobante) {
            $data['voucher_path'] = 'vouchers/demo_' . uniqid() . '.pdf';
        }

        Sale::create($data);
    }

    private function crearObservaciones()
    {
        $this->command->info('📝 Creando observaciones/logs...');

        $ventas = Sale::where('approval_status', 'observed')
            ->orWhere('approval_status', 'rejected')
            ->get();

        foreach ($ventas as $venta) {
            if ($venta->admin_observation) {
                SaleLog::create([
                    'sale_id' => $venta->id,
                    'user_id' => $this->admin->id,
                    'action' => $venta->approval_status === 'observed' ? 'observed' : 'rejected',
                    'old_status' => 'pending_admin',
                    'new_status' => $venta->approval_status,
                    'comment' => $venta->admin_observation,
                ]);
            }
        }

        $this->command->info('  ✓ Logs de observaciones/rechazos creados');
    }

    private function mostrarResumen()
    {
        $this->command->info('');
        $this->command->info('📊 RESUMEN DE DATOS CREADOS:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👥 Usuarios: ' . User::count());
        $this->command->info('🏅 Vendedores: ' . Seller::count());
        $this->command->info('💰 Ventas: ' . Sale::count());
        $this->command->info('📝 Logs: ' . SaleLog::count());
        $this->command->info('');
        $this->command->info('🔑 CREDENCIALES DE ACCESO:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📧 Admin: cambios_jotta@innodite.com');
        $this->command->info('🔒 Password: password');
        $this->command->info('');
    }
}
