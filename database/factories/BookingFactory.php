<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => rand(2, 50),
            'address_id' => rand(1, 100),
            'from' => $this->faker->time('H:i'),
            'to' => $this->faker->time('H:i'),
            'date' => $this->faker->date(),
            "grand_total" => $this->faker->numberBetween(1000, 50000),
            'status' => $this->faker->randomElement(['pending', 'cancelled', 'completed','accepted'])
        ];
    }
}
