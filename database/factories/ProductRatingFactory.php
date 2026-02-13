<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "attempts" => $this->faker->randomNumber(4, true),
            "rating" => $this->faker->randomFloat(2, 0, 5),
            "product_id" => rand(1, 100)
        ];
    }
}
