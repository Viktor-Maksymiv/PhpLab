<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'showtime_id' => Showtime::factory(),
            'seat_id' => Seat::factory(),
            'price' => fake()->randomFloat(2, 100, 300),
            'qr_code' => 'QR-'.strtoupper(fake()->unique()->bothify('????####')),
            'status' => fake()->randomElement(['reserved', 'paid']),
        ];
    }
}
