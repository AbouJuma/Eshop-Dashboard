<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->paragraph(2),
            'action_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'user_id' => rand(2, 50)
        ];
    }
}
