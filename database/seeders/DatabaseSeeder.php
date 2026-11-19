<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $genres = Genre::factory()->count(6)->create();
        $movies = Movie::factory()->count(8)->create();

        foreach ($movies as $movie) {
            $movie->genres()->sync($genres->random(2)->pluck('id')->all());
        }

        $halls = Hall::factory()->count(3)->create(['capacity' => 40, 'rows_count' => 5]);

        foreach ($halls as $hall) {
            for ($row = 1; $row <= 5; $row++) {
                for ($number = 1; $number <= 8; $number++) {
                    Seat::factory()->create([
                        'hall_id' => $hall->id,
                        'row_number' => $row,
                        'seat_number' => $number,
                    ]);
                }
            }
        }

        foreach ($movies as $movie) {
            Showtime::factory()
                ->count(2)
                ->state(fn () => ['hall_id' => $halls->random()->id])
                ->create(['movie_id' => $movie->id]);
        }

        $users = User::factory()->count(5)->create();
        $showtimes = Showtime::with('hall.seats')->get();

        for ($i = 0; $i < 10; $i++) {
            $showtime = $showtimes->random();
            $taken = Ticket::where('showtime_id', $showtime->id)->pluck('seat_id');
            $seats = $showtime->hall->seats->whereNotIn('id', $taken)->random(2);

            $order = Order::factory()->create([
                'user_id' => $users->random()->id,
                'showtime_id' => $showtime->id,
                'total_amount' => $showtime->base_price * 2,
                'status' => 'paid',
            ]);

            foreach ($seats as $seat) {
                Ticket::factory()->create([
                    'order_id' => $order->id,
                    'showtime_id' => $showtime->id,
                    'seat_id' => $seat->id,
                    'price' => $showtime->base_price,
                    'status' => 'paid',
                ]);
            }

            Payment::factory()->create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
            ]);
        }
    }
}
