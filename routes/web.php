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

Route::resource('sellers', SellerController::class);
Route::resource('liquidations', LiquidationController::class);
Route::resource('currencies', CurrencyController::class)->except(['show', 'destroy']);
Route::patch('currencies/{currency}/toggle-status', [CurrencyController::class, 'toggleStatus'])->name('currencies.toggleStatus');

Route::resource('corridors', CorridorController::class)->except(['show']);
Route::patch('corridors/{corridor}/toggle-status', [CorridorController::class, 'toggleStatus'])->name('corridors.toggleStatus');

Route::resource('currency-pairs', CurrencyPairController::class)->except(['show']);
Route::patch('currency-pairs/{currency_pair}/toggle-status', [CurrencyPairController::class, 'toggleStatus'])->name('currency-pairs.toggleStatus');

Route::get('corridor-matrix', [CorridorMatrixController::class, 'index'])->name('corridor-matrix.index');
Route::post('corridor-matrix/toggle', [CorridorMatrixController::class, 'toggle'])->name('corridor-matrix.toggle');

Route::get('/sellers-api', function () {
    return Seller::all();
});

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::resource('sales', SaleController::class);
Route::get('sales-bulk', [SaleController::class, 'bulkCreate'])->name('sales.bulk.create');
Route::post('sales/bulk', [SaleController::class, 'bulkStore'])->name('sales.bulk.store');
Route::get('sales-pending-seller', [SaleController::class, 'pendingSeller'])->name('sales.pending.seller');
Route::get('sales-pending-admin', [SaleController::class, 'pendingAdmin'])->name('sales.pending.admin');
Route::get('sales-observed', [SaleController::class, 'observed'])->name('sales.observed');
Route::get('sales-approved', [SaleController::class, 'approved'])->name('sales.approved');
Route::post('sales/{sale}/upload-voucher', [SaleController::class, 'uploadVoucher'])->name('sales.uploadVoucher');
Route::get('sales/{sale}/voucher', [SaleController::class, 'showVoucher'])->name('sales.showVoucher');
Route::get('sales/{sale}/voucher/download', [SaleController::class, 'downloadVoucher'])->name('sales.downloadVoucher');
Route::post('sales/{sale}/approve', [SaleController::class, 'approve'])->name('sales.approve');
Route::post('sales/{sale}/reject', [SaleController::class, 'reject'])->name('sales.reject');
Route::post('sales/{sale}/observe', [SaleController::class, 'observe'])->name('sales.observe');

// Tasas de cambio
Route::resource('exchange_rates', ExchangeRateController::class);
Route::post('exchange_rates/{exchangeRate}/activate', [ExchangeRateController::class, 'activate'])->name('exchange_rates.activate');


Route::get('/', function () {
    $rates = ExchangeRate::getActive();

    // Cargar todos los pares con sus divisas para el selector
    $pairs = \App\Models\ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
        ->whereNotNull('currency_pair_id')
        ->get()
        ->map(function($rate) {
            return [
                'id' => $rate->id,
                'from_code' => $rate->currencyPair->fromCurrency->code,
                'from_name' => $rate->currencyPair->fromCurrency->name,
                'from_country' => $rate->currencyPair->fromCurrency->country,
                'from_symbol' => $rate->currencyPair->fromCurrency->symbol,
                'flag' => $rate->currencyPair->fromCurrency->flag_emoji,
                'ves_rate' => $rate->ves_rate,
                'usd_rate' => $rate->usd_rate,
                'eur_rate' => $rate->eur_rate,
                'is_active' => $rate->is_active,
            ];
        });

    return view('welcome', compact('rates', 'pairs'));
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Redirigir según el rol del usuario
    if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('contador')) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->hasRole('vendedor')) {
        return redirect()->route('seller.dashboard');
    } elseif ($user->hasRole('cliente')) {
        return redirect()->route('client.dashboard');
    }

    // Por defecto, owner dashboard
    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/manage', [TransactionController::class, 'manage'])->name('transactions.manage');

    // Gestión de estado de transacciones
    Route::post('/transactions/{transaction}/observe', [TransactionController::class, 'observe'])->name('transactions.observe');
    Route::post('/transactions/{transaction}/process', [TransactionController::class, 'process'])->name('transactions.process');
    Route::post('/transactions/{transaction}/complete', [TransactionController::class, 'completeTransaction'])->name('transactions.complete');
    Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');

    // Dashboards específicos por rol
    Route::get('/owner-dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    Route::get('/seller-dashboard', [\App\Http\Controllers\SellerDashboardController::class, 'index'])->name('seller.dashboard');
    Route::get('/client-dashboard', [\App\Http\Controllers\ClientDashboardController::class, 'index'])->name('client.dashboard');

    // Reportes de vendedores
    Route::get('/reports/sellers/{seller}/performance', [SellerReportController::class, 'performance'])->name('reports.performance');
    Route::get('/reports/sellers/rankings', [SellerReportController::class, 'rankings'])->name('reports.rankings');

    // Exportaciones
    Route::get('/export/rankings/csv', [ExportController::class, 'rankingsCSV'])->name('export.rankings.csv');
    Route::get('/export/rankings/pdf', [ExportController::class, 'rankingsPDF'])->name('export.rankings.pdf');
    Route::get('/export/seller/{seller}/pdf', [ExportController::class, 'sellerReportPDF'])->name('export.seller.pdf');
    Route::get('/export/dashboard/csv', [ExportController::class, 'ownerDashboardCSV'])->name('export.dashboard.csv');
    Route::get('/export/dashboard/pdf', [ExportController::class, 'ownerDashboardPDF'])->name('export.dashboard.pdf');
});

require __DIR__.'/auth.php';
