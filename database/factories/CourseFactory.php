<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Category;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'description' => $this->faker->name,
            'urlImage' => $this->faker->imageUrl(),
            'totalLectures' => $this->faker->numberBetween(1, 32),
            'totalMinutes' => $this->faker->numberBetween(1, 1000),
            'points' => $this->faker->numberBetween(1, 99),
            'category_id' => Category::factory(),
        ];
    }
}
