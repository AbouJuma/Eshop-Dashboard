<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "user_id" => rand(1, 50),
            "make_id" => rand(1, 100),
            "model_id" => rand(1, 100),
        ];
    }
}
