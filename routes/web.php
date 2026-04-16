<?php

use App\Models\Seller;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExchangeRateController;

Route::resource('sellers', SellerController::class);

Route::get('/sellers-api', function () {
    return Seller::all();
});

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::resource('sales', SaleController::class);
Route::get('sales-bulk', [SaleController::class, 'bulkCreate'])->name('sales.bulk.create');
Route::post('sales/bulk', [SaleController::class, 'bulkStore'])->name('sales.bulk.store');

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
});

require __DIR__.'/auth.php';
