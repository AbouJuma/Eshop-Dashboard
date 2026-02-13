<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubServiceFactory extends Factory
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
            'service_id' => rand(1, 4),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'discount' => $this->faker->numberBetween(0, 100),
            'status' => rand(0, 1),
        ];
    }
}
