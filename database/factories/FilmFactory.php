<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
          return [
                'title' => $this->faker->sentence(3),
                'description' => $this->faker->realText(300, 2),
                'duration' => $this->faker->numberBetween(60, 150),
                'country' => $this->faker->country,
                'poster' => 'public/poster' . $this->faker->unique()->numberBetween(1, 5) . '.jpg',
            ];

    }
}
