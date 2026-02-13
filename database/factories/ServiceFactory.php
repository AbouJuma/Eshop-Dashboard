<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
            'description' => $this->faker->sentence(),
            'status' => rand(0, 1),
        ];
    }
}
