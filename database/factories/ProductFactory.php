<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->word(),
            "price" => $this->faker->numberBEtween(1000, 50000),
            "discount" => rand(0, 100),
            "pin" => rand(0, 1),
            "image" => $this->faker->imageUrl(),
            "product_category_id" => rand(1, 10),
        ];
    }
}
