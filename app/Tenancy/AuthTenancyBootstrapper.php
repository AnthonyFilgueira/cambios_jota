<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class AuthTenancyBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        Config::set('auth.providers.users.model', \App\Models\User::class);
        Auth::forgetGuards();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function revert(): void
    {
        Config::set('auth.providers.users.model', \App\Models\Central\CentralAdmin::class);
        Auth::forgetGuards();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
