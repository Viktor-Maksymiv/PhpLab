<?php

use App\Http\Controllers\Api\GenreController as ApiGenreController;
use App\Http\Controllers\Api\HallController as ApiHallController;
use App\Http\Controllers\Api\MovieController as ApiMovieController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\SalesReportController as ApiSalesReportController;
use App\Http\Controllers\Api\ShowtimeController as ApiShowtimeController;
use App\Http\Controllers\Api\TicketController as ApiTicketController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/health', fn () => response()->json(['status' => 'OK', 'timestamp' => now()]));

Route::get('/about', function () {
    return response()->json([
        'system' => 'Електронна система продажу квитків до кінотеатру',
        'laravel' => app()->version(),
        'php' => PHP_VERSION,
    ]);
})->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

Route::post('/logout', [WebAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('movies', MovieController::class)->except(['index', 'show']);
    Route::resource('genres', GenreController::class)->except(['index', 'show']);
    Route::resource('halls', HallController::class)->except(['index', 'show']);
    Route::resource('seats', SeatController::class)->except(['index', 'show']);
    Route::resource('showtimes', ShowtimeController::class)->except(['index', 'show']);
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/payment', [OrderController::class, 'pay'])->name('orders.pay');
    Route::resource('tickets', TicketController::class);
    Route::resource('users', UserController::class)->except(['show']);
});

Route::resource('movies', MovieController::class)->only(['index', 'show']);
Route::resource('genres', GenreController::class)->only(['index']);
Route::resource('halls', HallController::class)->only(['index', 'show']);
Route::resource('seats', SeatController::class)->only(['index']);
Route::resource('showtimes', ShowtimeController::class)->only(['index', 'show']);

Route::prefix('api')->name('api.')->group(function () {
    Route::get('/reports/sales', [ApiSalesReportController::class, 'summary'])->name('reports.sales');

    Route::prefix('genres')->name('genres.')->group(function () {
        Route::get('/', [ApiGenreController::class, 'index'])->name('index');
        Route::post('/', [ApiGenreController::class, 'store'])->name('store');
        Route::get('/{id}', [ApiGenreController::class, 'show'])->whereNumber('id')->name('show');
    });

    Route::prefix('movies')->name('movies.')->group(function () {
        Route::get('/', [ApiMovieController::class, 'index'])->name('index');
        Route::post('/', [ApiMovieController::class, 'store'])->name('store');
        Route::get('/{id}', [ApiMovieController::class, 'show'])->whereNumber('id')->name('show');
        Route::put('/{id}', [ApiMovieController::class, 'update'])->whereNumber('id')->name('update');
    });

    Route::prefix('halls')->name('halls.')->group(function () {
        Route::get('/', [ApiHallController::class, 'index'])->name('index');
        Route::get('/{id}', [ApiHallController::class, 'show'])->whereNumber('id')->name('show');
    });

    Route::prefix('showtimes')->name('showtimes.')->group(function () {
        Route::get('/', [ApiShowtimeController::class, 'index'])->name('index');
        Route::post('/', [ApiShowtimeController::class, 'store'])->name('store');
        Route::get('/{id}', [ApiShowtimeController::class, 'show'])->whereNumber('id')->name('show');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index'])->name('index');
        Route::post('/', [ApiOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [ApiOrderController::class, 'show'])->whereNumber('id')->name('show');
        Route::post('/{id}/payment', [ApiOrderController::class, 'pay'])->whereNumber('id')->name('pay');
    });

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [ApiTicketController::class, 'index'])->name('index');
        Route::post('/', [ApiTicketController::class, 'store'])->middleware('sales.channel')->name('store');
        Route::get('/{id}', [ApiTicketController::class, 'show'])->whereNumber('id')->name('show');
    });
});
