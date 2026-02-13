<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->randomModel(),
            "make_id" => rand(1, 15)
        ];

    }

    //random car model
    function randomModel() : String {
        $models=[
            "rav 4","collora","carina","camry","corona","crown","mark x","mark 2","mark 2 blit","mark 2 grande","mark 2 ir-v","mark 2 qualis","mark 2 tourer v"
        ];
        return $models[rand(0,count($models)-1)];
    }
}
