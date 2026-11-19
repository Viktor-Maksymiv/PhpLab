<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ShowtimeController;
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

Route::prefix('genres')->name('genres.')->group(function () {
    Route::get('/', [GenreController::class, 'index'])->name('index');
    Route::post('/', [GenreController::class, 'store'])->name('store');
    Route::get('/{id}', [GenreController::class, 'show'])->whereNumber('id')->name('show');
});

Route::prefix('movies')->name('movies.')->group(function () {
    Route::get('/', [MovieController::class, 'index'])->name('index');
    Route::post('/', [MovieController::class, 'store'])->name('store');
    Route::get('/{id}', [MovieController::class, 'show'])->whereNumber('id')->name('show');
    Route::put('/{id}', [MovieController::class, 'update'])->whereNumber('id')->name('update');
});

Route::prefix('halls')->name('halls.')->group(function () {
    Route::get('/', [HallController::class, 'index'])->name('index');
    Route::get('/{id}', [HallController::class, 'show'])->whereNumber('id')->name('show');
});

Route::prefix('showtimes')->name('showtimes.')->group(function () {
    Route::get('/', [ShowtimeController::class, 'index'])->name('index');
    Route::post('/', [ShowtimeController::class, 'store'])->name('store');
    Route::get('/{id}', [ShowtimeController::class, 'show'])->whereNumber('id')->name('show');
});

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{id}', [OrderController::class, 'show'])->whereNumber('id')->name('show');
    Route::post('/{id}/payment', [OrderController::class, 'pay'])->whereNumber('id')->name('pay');
});

Route::prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::post('/', [TicketController::class, 'store'])->middleware('sales.channel')->name('store');
    Route::get('/{id}', [TicketController::class, 'show'])->whereNumber('id')->name('show');
});
