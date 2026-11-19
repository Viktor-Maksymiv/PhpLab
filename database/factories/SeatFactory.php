<?php

namespace Database\Factories;

use App\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hall_id' => Hall::factory(),
            'row_number' => fake()->numberBetween(1, 12),
            'seat_number' => fake()->numberBetween(1, 20),
            'type' => fake()->randomElement(['standard', 'vip', 'sofa']),
        ];
    }
}
