<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dog>
 */
class DogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'breed' => $this->faker->word,
            'gender' => $this->faker->randomElement(['M', 'F']),
            'is_public' => $this->faker->boolean,
            'img_path' => null,
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
