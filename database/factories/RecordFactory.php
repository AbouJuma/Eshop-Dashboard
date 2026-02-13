<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['pending', 'accepted', 'completed', 'denied']),
            'content' => $this->faker->paragraph(2),
            'action_date' => $this->faker->dateTime(),
            'user_id' => rand(2, 50)
        ];
    }
}
