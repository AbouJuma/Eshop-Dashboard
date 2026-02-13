<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'location' => $this->faker->address,
            'date' => $this->faker->date,
            'from' => $this->faker->time('H:i'),
            'image' => $this->faker->imageUrl(),
            'visibility' => rand(0,1),
            'to' => $this->faker->time('H:i'),
        ];
    }
}
