<?php

namespace Database\Factories;

use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_module_id' => CourseModule::factory(),
            'title' => rtrim(fake()->sentence(3), '.'),
            'vimeo_id' => (string) fake()->numberBetween(100000000, 999999999),
            'description' => fake()->optional()->paragraph(),
            'position' => 0,
        ];
    }
}
