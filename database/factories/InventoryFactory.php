<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quantity' => $this->faker->randomNumber(2),
            'status' => $this->faker->randomElement(['OUT', 'LOW', 'NORM']),
            'product_id' => rand(1, 100)
        ];
    }
}
