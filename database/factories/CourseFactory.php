<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
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
        return [
            'course_code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{2}'),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(4),
            'category' => $this->faker->randomElement(['programming', 'web_design', 'photography', 'language']),
            'instructor_id' => $this->faker->numberBetween(2, 3),
        ];
    }
}
