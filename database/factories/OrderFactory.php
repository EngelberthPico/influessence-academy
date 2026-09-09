<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_payment_intent_id' => null,
            'stripe_checkout_session_id' => null,
            'status' => 'pending',
            'total_cents' => fake()->numberBetween(2000, 20000),
            'currency' => 'usd',
            'paid_at' => null,
        ];
    }
}
