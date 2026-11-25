<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Order;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Support\Collection;

class FormOptions
{
    public static function genres(): Collection
    {
        return Genre::query()->orderBy('name')->get();
    }

    public static function users(): Collection
    {
        return User::query()->orderBy('name')->pluck('name', 'id');
    }

    public static function movies(): Collection
    {
        return Movie::query()->orderBy('title')->pluck('title', 'id');
    }

    public static function halls(): Collection
    {
        return Hall::query()->orderBy('name')->pluck('name', 'id');
    }

    public static function showtimes(): Collection
    {
        return Showtime::query()
            ->with(['movie:id,title', 'hall:id,name'])
            ->orderBy('start_at')
            ->get()
            ->mapWithKeys(fn (Showtime $showtime) => [
                $showtime->id => "{$showtime->movie->title}, {$showtime->start_at->format('d.m.Y H:i')}, {$showtime->hall->name}",
            ]);
    }

    public static function orders(?User $user = null): Collection
    {
        return Order::query()
            ->with('customer:id,name')
            ->when($user !== null && ! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Order $order) => [$order->id => "#{$order->id}, {$order->customer->name}"]);
    }

    public static function seats(): Collection
    {
        return Seat::query()
            ->with('hall:id,name')
            ->orderBy('hall_id')
            ->orderBy('row_number')
            ->orderBy('seat_number')
            ->get()
            ->mapWithKeys(fn (Seat $seat) => [
                $seat->id => "{$seat->hall->name}, ряд {$seat->row_number}, місце {$seat->seat_number}",
            ]);
    }
}
