<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "action" => $this->action(),
            "platform" => $this->platform(),
            "user_id" => rand(1, 10),
            "description" => $this->faker->sentence()
        ];
    }

    public function action()
    {
        $_ =["edit","delete","access","other"];
        return $_[rand(0,3)];
    }

    public function platform()
    {
        $_ = ["application","dashboard"];
        return $_[rand(0,1)];
    }
}
