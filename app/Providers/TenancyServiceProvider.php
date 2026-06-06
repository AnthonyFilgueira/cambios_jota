<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    public static string $controllerNamespace = '';

    public function events(): array
    {
        return [
            Events\CreatingTenant::class  => [],
            Events\TenantCreated::class   => [
                JobPipeline::make([
                    \App\Jobs\Tenancy\CreateDatabase::class,
                    \App\Jobs\Tenancy\MigrateDatabase::class,
                    \App\Jobs\Tenancy\CreateInitialTenantAdmin::class,
                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(true), // async en producción
            ],
            Events\SavingTenant::class    => [],
            Events\TenantSaved::class     => [],
            Events\UpdatingTenant::class  => [],
            Events\TenantUpdated::class   => [],
            Events\DeletingTenant::class  => [],
            Events\TenantDeleted::class   => [
                JobPipeline::make([
                    Jobs\DeleteDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false),
            ],

            Events\DomainCreated::class   => [],
            Events\DomainSaved::class     => [],
            Events\DomainDeleted::class   => [],

            Events\DatabaseCreated::class  => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class   => [],
            Events\DatabaseDeleted::class  => [],

            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class  => [
                Listeners\BootstrapTenancy::class,
            ],

            Events\EndingTenancy::class   => [],
            Events\TenancyEnded::class    => [
                Listeners\RevertToCentralContext::class,
            ],

            Events\TenancyBootstrapped::class        => [],
            Events\RevertedToCentralContext::class   => [],
        ];
    }

    public function register(): void {}

    public function boot(): void
    {
        $this->bootEvents();
        $this->mapRoutes();
        $this->makeTenancyMiddlewareHighestPriority();
    }

    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }
                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes(): void
    {
        $this->app->booted(function () {
            if (file_exists(base_path('routes/tenant.php'))) {
                Route::namespace(static::$controllerNamespace)
                    ->group(base_path('routes/tenant.php'));
            }
        });
    }

    protected function makeTenancyMiddlewareHighestPriority(): void
    {
        $tenancyMiddleware = [
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyBySubdomain::class,
        ];

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]
                ->prependToMiddlewarePriority($middleware);
        }
    }
}
