<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MfgYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "model_id" => rand(1, 100),
            "year" => $this->setYear()
        ];
    }

    public function setYear()
    {
        $_=[date("Y", strtotime("-5 year")), date("Y", strtotime("-3 year")), date('Y'), date("Y",strtotime("-2 year"))];
        return $_[rand(0,3)];
    }
}
