<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GenreController;
use App\Http\Controllers\Api\V1\HallController;
use App\Http\Controllers\Api\V1\MovieController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\SeatController;
use App\Http\Controllers\Api\V1\ShowtimeController;
use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::apiResource('movies', MovieController::class)->only(['index', 'show']);
    Route::apiResource('genres', GenreController::class)->only(['index', 'show']);
    Route::apiResource('halls', HallController::class)->only(['index', 'show']);
    Route::apiResource('seats', SeatController::class)->only(['index', 'show']);
    Route::apiResource('showtimes', ShowtimeController::class)->only(['index', 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::apiResource('movies', MovieController::class)->except(['index', 'show']);
        Route::apiResource('genres', GenreController::class)->except(['index', 'show']);
        Route::apiResource('halls', HallController::class)->except(['index', 'show']);
        Route::apiResource('seats', SeatController::class)->except(['index', 'show']);
        Route::apiResource('showtimes', ShowtimeController::class)->except(['index', 'show']);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('tickets', TicketController::class);
    });
});
