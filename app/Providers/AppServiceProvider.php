<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use App\Models\Sale;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Models\Seller;
use App\Models\User;
use App\Models\Country;
use App\Models\Bank;
use App\Models\BusinessAccount;
use App\Models\IncentiveRule;
use App\Observers\SaleObserver;
use App\Observers\ExchangeRateObserver;
use App\Observers\AuditObserver;
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

        // Auditoría automática en todas las entidades críticas
        $auditModels = [Transaction::class, Seller::class, User::class,
                        Country::class, Bank::class, BusinessAccount::class,
                        IncentiveRule::class];
        foreach ($auditModels as $model) {
            $model::observe(AuditObserver::class);
        }

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
