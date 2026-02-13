<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'time' => $this->faker->time('H:i'),
            'order_id' => rand(1, 50),
            'user_id' => rand(2, 50),
            'address_id' => rand(1, 100),
            'status' => $this->faker->randomElement(['PENDING', 'DELIVERED', 'CANCELED'])
        ];
    }
}
