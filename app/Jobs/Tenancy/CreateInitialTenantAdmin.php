<?php

namespace App\Jobs\Tenancy;

use App\Models\Central\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class CreateInitialTenantAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Tenant $tenant) {}

    public function handle(): void
    {
        tenancy()->initialize($this->tenant);

        // Crear roles y permisos base en la BD del tenant
        \Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);

        // Crear el administrador inicial del tenant
        $admin = User::create([
            'name'              => $this->tenant->admin_name,
            'email'             => $this->tenant->admin_email,
            'password'          => Hash::make($this->tenant->admin_password_plain ?? str()->random(16)),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super-admin');

        // Marcar el tenant como activo
        tenancy()->end();

        $this->tenant->update(['status' => 'active']);
    }
}
