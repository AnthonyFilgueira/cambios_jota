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

Route::resource('sellers', SellerController::class);
Route::resource('liquidations', LiquidationController::class);

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
});

require __DIR__.'/auth.php';
