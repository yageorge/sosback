<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Section;
use App\Models\Lecture;

class LectureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lecture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'content' => $this->faker->text,
            'containsVideo' => $this->faker->boolean(),
            'urlVideo' => $this->faker->url,
            'duration' => $this->faker->randomNumber(),
            'section_id' => Section::factory(),
        ];
    }
}
