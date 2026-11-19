<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'duration_minutes' => fake()->numberBetween(80, 180),
            'age_rating' => fake()->randomElement(['0+', '6+', '12+', '16+', '18+']),
            'release_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'poster_url' => '/posters/'.fake()->unique()->slug(2).'.jpg',
        ];
    }
}
