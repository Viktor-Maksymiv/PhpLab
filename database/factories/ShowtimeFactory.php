<?php

namespace Database\Factories;

use App\Models\Hall;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShowtimeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'hall_id' => Hall::factory(),
            'start_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'base_price' => fake()->randomFloat(2, 100, 300),
            'status' => 'scheduled',
        ];
    }
}
