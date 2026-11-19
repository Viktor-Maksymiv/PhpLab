<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 100, 600),
            'method' => fake()->randomElement(['card', 'cash', 'apple_pay']),
            'transaction_id' => fake()->unique()->uuid(),
            'paid_at' => now(),
            'status' => 'success',
        ];
    }
}
