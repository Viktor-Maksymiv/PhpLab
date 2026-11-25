<?php

namespace App\Providers;

use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\Showtime;
use App\Policies\AdminPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        foreach ([Genre::class, Hall::class, Movie::class, Seat::class, Showtime::class] as $model) {
            Gate::policy($model, AdminPolicy::class);
        }
    }
}
