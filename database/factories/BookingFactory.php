<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
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
            'course_id' => Course::factory(),
            'calendly_event_uri' => fake()->unique()->url(),
            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'status' => 'scheduled',
        ];
    }
}
