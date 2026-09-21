<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'price_cents' => fake()->numberBetween(2000, 20000),
            'currency' => 'usd',
            'vimeo_id' => null,
            'is_published' => true,
            'is_best_seller' => false,
            'published_at' => now(),
        ];
    }

    public function bestSeller(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_best_seller' => true,
        ]);
    }
}
