<?php

namespace Database\Factories;

use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'showtime_id' => Showtime::factory(),
            'total_amount' => fake()->randomFloat(2, 100, 600),
            'status' => fake()->randomElement(['pending', 'paid', 'cancelled']),
            'expires_at' => now()->addMinutes(10),
        ];
    }
}
