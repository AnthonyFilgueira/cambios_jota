<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Sale;
use App\Observers\SaleObserver;
use App\Events\SaleCompleted;
use App\Listeners\SendVoucherUploadedNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sale::observe(SaleObserver::class);

        // Registrar listener de SaleCompleted
        Event::listen(
            SaleCompleted::class,
            SendVoucherUploadedNotification::class,
        );
    }
}
