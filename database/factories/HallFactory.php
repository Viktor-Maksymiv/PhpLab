<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HallFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Зал '.fake()->unique()->numberBetween(1, 9999),
            'capacity' => fake()->numberBetween(40, 200),
            'format' => fake()->randomElement(['2D', '3D', 'IMAX', '4DX']),
            'rows_count' => fake()->numberBetween(5, 12),
            'is_active' => true,
        ];
    }
}
