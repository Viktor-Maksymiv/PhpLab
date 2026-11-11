<?php

use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', fn () => response()->json(['status' => 'OK', 'timestamp' => now()]));

Route::get('/about', function () {
    return response()->json([
        'system' => 'Електронна система продажу квитків до кінотеатру',
        'laravel' => app()->version(),
        'php' => PHP_VERSION,
    ]);
})->name('about');

Route::get('/reports/sales', [SalesReportController::class, 'summary'])->name('reports.sales');

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::post('/', [TicketController::class, 'store'])->middleware('sales.channel')->name('store');
    Route::get('/{id}', [TicketController::class, 'show'])->whereNumber('id')->name('show');
});
