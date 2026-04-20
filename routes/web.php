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

Route::resource('sellers', SellerController::class);
Route::resource('liquidations', LiquidationController::class);
Route::resource('currencies', CurrencyController::class)->except(['show', 'destroy']);
Route::patch('currencies/{currency}/toggle-status', [CurrencyController::class, 'toggleStatus'])->name('currencies.toggleStatus');

Route::resource('corridors', CorridorController::class)->except(['show']);
Route::patch('corridors/{corridor}/toggle-status', [CorridorController::class, 'toggleStatus'])->name('corridors.toggleStatus');

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
    return view('welcome', compact('rates'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/owner-dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

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
