<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Models\Sale;
use App\Models\ExchangeRate;
use App\Observers\SaleObserver;
use App\Observers\ExchangeRateObserver;
use App\Events\SaleCompleted;
use App\Listeners\SendVoucherUploadedNotification;
use App\Http\View\Composers\TransactionFormComposer;

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
        ExchangeRate::observe(ExchangeRateObserver::class);

        // Registrar listener de SaleCompleted
        Event::listen(
            SaleCompleted::class,
            SendVoucherUploadedNotification::class,
        );

        // View Composer para formulario de transacciones
        // Asegura que $pairs y $rates estén siempre disponibles incluso en redirects
        View::composer('transactions.create', TransactionFormComposer::class);
    }
}
