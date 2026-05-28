<?php

use App\Models\Seller;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\LiquidationController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\SellerReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CorridorController;
use App\Http\Controllers\CurrencyPairController;
use App\Http\Controllers\CorridorMatrixController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BusinessAccountController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\SettingController;

// ─── RUTAS PÚBLICAS ───────────────────────────────────────────────────────────

// Simulador público
Route::get('/', function () {
    $rates = ExchangeRate::getActive();

    $pairs = \App\Models\ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
        ->whereNotNull('currency_pair_id')
        ->get()
        ->map(function ($rate) {
            return [
                'id'           => $rate->id,
                'from_code'    => $rate->currencyPair->fromCurrency->code,
                'from_name'    => $rate->currencyPair->fromCurrency->name,
                'from_country' => $rate->currencyPair->fromCurrency->country,
                'from_symbol'  => $rate->currencyPair->fromCurrency->symbol,
                'flag'         => $rate->currencyPair->fromCurrency->flag_emoji,
                'ves_rate'     => $rate->ves_rate,
                'usd_rate'     => $rate->usd_rate,
                'eur_rate'     => $rate->eur_rate,
                'is_active'    => $rate->is_active,
            ];
        });

    $bonusPreview = app(\App\Services\IncentiveService::class)->getReceptorPreview(auth()->user(), 0);

    return view('welcome', compact('rates', 'pairs', 'bonusPreview'));
});

// API pública: buscar vendedor por código (usada en el formulario de transacción)
Route::get('/api/sellers/search/{code}', function ($code) {
    $seller = Seller::with(['businessAccounts.bank.country'])
        ->where('code', strtoupper($code))
        ->first();

    if (!$seller) {
        return response()->json(['error' => 'Código de vendedor no encontrado'], 404);
    }

    $accounts = $seller->businessAccounts->where('active', true)->map(function ($account) {
        return [
            'id'             => $account->id,
            'alias'          => $account->alias ?: $account->bank->name,
            'bank_name'      => $account->bank->name,
            'account_number' => $account->account_number,
            'account_type'   => $account->account_type_label,
            'account_holder' => $account->account_holder,
            'dni_ruc'        => $account->dni_ruc,
            'country'        => $account->bank->country->name ?? null,
        ];
    })->values();

    return response()->json([
        'success'  => true,
        'seller'   => [
            'id'   => $seller->id,
            'code' => $seller->code,
            'name' => $seller->name,
        ],
        'accounts' => $accounts,
    ]);
})->name('api.sellers.search');

// ─── DASHBOARD: REDIRIGE SEGÚN ROL ───────────────────────────────────────────

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('contador')) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->hasRole('vendedor')) {
        return redirect()->route('seller.dashboard');
    } elseif ($user->hasRole('cliente')) {
        return redirect()->route('client.dashboard');
    }

    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── RUTAS AUTENTICADAS ───────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboards
    Route::get('/owner-dashboard',  [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    Route::get('/seller-dashboard', fn() => redirect()->route('seller.bandeja'))->name('seller.dashboard');
    Route::get('/client-dashboard', [\App\Http\Controllers\ClientDashboardController::class, 'index'])->name('client.dashboard');

    // Panel del Vendedor — bandeja de solicitudes
    Route::prefix('seller')->name('seller.')->group(function () {
        Route::get('/bandeja',                          [\App\Http\Controllers\SellerTransactionController::class, 'bandeja'])->name('bandeja');
        Route::get('/mi-codigo',                        [\App\Http\Controllers\SellerTransactionController::class, 'miCodigo'])->name('mi-codigo');
        Route::get('/solicitud/{transaction}',          [\App\Http\Controllers\SellerTransactionController::class, 'show'])->name('solicitud.show');
        Route::post('/solicitud/{transaction}/approve', [\App\Http\Controllers\SellerTransactionController::class, 'approve'])->name('solicitud.approve');
        Route::post('/solicitud/{transaction}/observe', [\App\Http\Controllers\SellerTransactionController::class, 'observe'])->name('solicitud.observe');
        Route::post('/solicitud/{transaction}/deny',    [\App\Http\Controllers\SellerTransactionController::class, 'deny'])->name('solicitud.deny');
    });

    // Comisiones por vendedor (admin)
    Route::get('sellers/{seller}/commissions',        [SellerController::class, 'commissions'])->name('sellers.commissions');
    Route::post('sellers/{seller}/commissions',       [SellerController::class, 'storeCommission'])->name('sellers.commissions.store');

    // Transacciones (cliente crea, admin/vendedor gestiona)
    Route::get('/transactions/seller-accounts',           [TransactionController::class, 'getSellerAccounts'])->name('transactions.sellerAccounts');
    Route::get('/transactions/document-types',            [TransactionController::class, 'getDocumentTypes'])->name('transactions.documentTypes');
    Route::get('/transactions',                           [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create',                    [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions',                          [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/manage',                    [TransactionController::class, 'manage'])->name('transactions.manage');
    Route::post('/transactions/{transaction}/observe',    [TransactionController::class, 'observe'])->name('transactions.observe');
    Route::post('/transactions/{transaction}/process',    [TransactionController::class, 'process'])->name('transactions.process');
    Route::post('/transactions/{transaction}/complete',              [TransactionController::class, 'completeTransaction'])->name('transactions.complete');
    Route::post('/transactions/{transaction}/upload-final-voucher', [TransactionController::class, 'uploadFinalVoucher'])->name('transactions.uploadFinalVoucher');
    Route::get('/transactions/{transaction}/confirmacion',          [TransactionController::class, 'confirmacion'])->name('transactions.confirmacion');
    Route::post('/transactions/{transaction}/cancel',               [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('/transactions/{transaction}/edit',                  [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::post('/transactions/{transaction}/update',               [TransactionController::class, 'update'])->name('transactions.update');

    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/conciliation', [ReportController::class, 'conciliation'])->name('reports.conciliation');
    Route::get('reports/export/transactions', [ReportController::class, 'exportTransactions'])->name('reports.export.transactions');
    Route::get('reports/export/conciliation', [ReportController::class, 'exportConciliation'])->name('reports.export.conciliation');

    // Ventas (vendedor registra, admin aprueba)
    Route::resource('sales', SaleController::class);
    Route::get('sales-bulk',                              [SaleController::class, 'bulkCreate'])->name('sales.bulk.create');
    Route::post('sales/bulk',                             [SaleController::class, 'bulkStore'])->name('sales.bulk.store');
    Route::get('sales-pending-seller',                    [SaleController::class, 'pendingSeller'])->name('sales.pending.seller');
    Route::get('sales-pending-admin',                     [SaleController::class, 'pendingAdmin'])->name('sales.pending.admin');
    Route::get('sales-observed',                          [SaleController::class, 'observed'])->name('sales.observed');
    Route::get('sales-approved',                          [SaleController::class, 'approved'])->name('sales.approved');
    Route::post('sales/{sale}/upload-voucher',            [SaleController::class, 'uploadVoucher'])->name('sales.uploadVoucher');
    Route::get('sales/{sale}/voucher',                    [SaleController::class, 'showVoucher'])->name('sales.showVoucher');
    Route::get('sales/{sale}/voucher/download',           [SaleController::class, 'downloadVoucher'])->name('sales.downloadVoucher');
    Route::post('sales/{sale}/approve',                   [SaleController::class, 'approve'])->name('sales.approve');
    Route::post('sales/{sale}/reject',                    [SaleController::class, 'reject'])->name('sales.reject');
    Route::post('sales/{sale}/observe',                   [SaleController::class, 'observe'])->name('sales.observe');

    // Monedero
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');

    // Vendedores (solo admin)
    Route::resource('sellers', SellerController::class);
    Route::get('/sellers-api', fn() => Seller::all());

    // Liquidaciones
    Route::resource('liquidations', LiquidationController::class);

    // Tasas de cambio
    Route::resource('exchange_rates', ExchangeRateController::class);
    Route::post('exchange_rates/{exchangeRate}/activate', [ExchangeRateController::class, 'activate'])->name('exchange_rates.activate');

    // Divisas, pares y corredores
    Route::resource('currencies', CurrencyController::class)->except(['show', 'destroy']);
    Route::patch('currencies/{currency}/toggle-status',           [CurrencyController::class, 'toggleStatus'])->name('currencies.toggleStatus');
    Route::resource('corridors', CorridorController::class)->except(['show']);
    Route::patch('corridors/{corridor}/toggle-status',            [CorridorController::class, 'toggleStatus'])->name('corridors.toggleStatus');
    Route::resource('currency-pairs', CurrencyPairController::class)->except(['show']);
    Route::patch('currency-pairs/{currency_pair}/toggle-status',  [CurrencyPairController::class, 'toggleStatus'])->name('currency-pairs.toggleStatus');
    Route::get('corridor-matrix',                                  [CorridorMatrixController::class, 'index'])->name('corridor-matrix.index');
    Route::post('corridor-matrix/toggle',                          [CorridorMatrixController::class, 'toggle'])->name('corridor-matrix.toggle');

    // Países, bancos y cuentas del negocio (REQ 11)
    Route::get('countries',                                            [CountryController::class, 'index'])->name('countries.index');
    Route::post('countries',                                           [CountryController::class, 'store'])->name('countries.store');
    Route::get('countries/{country}',                                  [CountryController::class, 'show'])->name('countries.show');
    Route::put('countries/{country}',                                  [CountryController::class, 'update'])->name('countries.update');
    Route::patch('countries/{country}/toggle-active',                  [CountryController::class, 'toggleActive'])->name('countries.toggleActive');
    Route::post('countries/{country}/payment-methods',                             [CountryController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::put('countries/{country}/payment-methods/{paymentMethod}',              [CountryController::class, 'updatePaymentMethod'])->name('payment-methods.update');
    Route::patch('countries/{country}/payment-methods/{paymentMethod}/toggle',     [CountryController::class, 'togglePaymentMethod'])->name('payment-methods.toggle');
    Route::delete('countries/{country}/payment-methods/{paymentMethod}',           [CountryController::class, 'destroyPaymentMethod'])->name('payment-methods.destroy');
    Route::post('countries/{country}/document-types',                             [CountryController::class, 'storeDocumentType'])->name('document-types.store');
    Route::put('countries/{country}/document-types/{documentType}',              [CountryController::class, 'updateDocumentType'])->name('document-types.update');
    Route::patch('countries/{country}/document-types/{documentType}/toggle',     [CountryController::class, 'toggleDocumentType'])->name('document-types.toggle');
    Route::delete('countries/{country}/document-types/{documentType}',           [CountryController::class, 'destroyDocumentType'])->name('document-types.destroy');
    Route::post('countries/{country}/banks',                           [BankController::class, 'store'])->name('banks.store');
    Route::put('countries/{country}/banks/{bank}',                     [BankController::class, 'update'])->name('banks.update');
    Route::patch('countries/{country}/banks/{bank}/toggle-active',     [BankController::class, 'toggleActive'])->name('banks.toggleActive');
    Route::post('countries/{country}/accounts',                        [BusinessAccountController::class, 'store'])->name('business-accounts.store');
    Route::put('countries/{country}/accounts/{businessAccount}',       [BusinessAccountController::class, 'update'])->name('business-accounts.update');
    Route::patch('countries/{country}/accounts/{businessAccount}/toggle-active', [BusinessAccountController::class, 'toggleActive'])->name('business-accounts.toggleActive');
    Route::post('business-accounts/{businessAccount}/assign',          [BusinessAccountController::class, 'assign'])->name('business-accounts.assign');
    Route::delete('business-accounts/{businessAccount}/unassign/{seller}', [BusinessAccountController::class, 'unassign'])->name('business-accounts.unassign');

    // Reportes y exportaciones
    Route::get('/reports/sellers/{seller}/performance', [SellerReportController::class, 'performance'])->name('reports.performance');
    Route::get('/reports/sellers/rankings',             [SellerReportController::class, 'rankings'])->name('reports.rankings');
    Route::get('/export/rankings/csv',                  [ExportController::class, 'rankingsCSV'])->name('export.rankings.csv');
    Route::get('/export/rankings/pdf',                  [ExportController::class, 'rankingsPDF'])->name('export.rankings.pdf');
    Route::get('/export/seller/{seller}/pdf',           [ExportController::class, 'sellerReportPDF'])->name('export.seller.pdf');
    Route::get('/export/dashboard/csv',                 [ExportController::class, 'ownerDashboardCSV'])->name('export.dashboard.csv');
    Route::get('/export/dashboard/pdf',                 [ExportController::class, 'ownerDashboardPDF'])->name('export.dashboard.pdf');

    // Administración: roles y permisos
    Route::get('admin/roles',                               [RolePermissionController::class, 'index'])->name('admin.roles.index');
    Route::get('admin/roles/{role}/permissions',            [RolePermissionController::class, 'show'])->name('admin.roles.show');
    Route::post('admin/roles/{role}/toggle-permission',     [RolePermissionController::class, 'togglePermission'])->name('admin.roles.togglePermission');
    Route::post('admin/assign-role',                        [RolePermissionController::class, 'assignRoleToUser'])->name('admin.assignRole');

    // Auditoría
    Route::get('admin/audit-logs',                          [AuditController::class, 'index'])->name('admin.audit-logs');

    // Configuración global
    Route::get('admin/settings',                            [SettingController::class, 'index'])->name('admin.settings');
    Route::post('admin/settings',                           [SettingController::class, 'update'])->name('admin.settings.update');

    // Incentivos (REQ 9)
    Route::get('admin/incentives',                          [IncentiveController::class, 'index'])->name('admin.incentives.index');
    Route::post('admin/incentives',                         [IncentiveController::class, 'store'])->name('admin.incentives.store');
    Route::patch('admin/incentives/{incentive}/toggle',     [IncentiveController::class, 'toggleActive'])->name('admin.incentives.toggle');
    Route::delete('admin/incentives/{incentive}',           [IncentiveController::class, 'destroy'])->name('admin.incentives.destroy');
});

require __DIR__.'/auth.php';
