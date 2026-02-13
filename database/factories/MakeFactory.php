<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MakeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->randomMake(),
        ];

    }

    //random make
    function randomMake() : String {
        $makes=[
            "Toyota","Honda","Nissan","Mitsubishi","Suzuki","Isuzu","Mazda","Subaru","Daihatsu","Lexus","Acura","Infiniti","Hyundai","Kia","Daewoo","Ssangyong","Chevrolet","Ford","Chrysler","Dodge","Jeep","Tesla","GMC","Buick","Cadillac","Mercedes-Benz","BMW","Audi","Volkswagen","Volvo","Peugeot","Renault","Citroen","Fiat","Alfa Romeo","Lamborghini","Maserati","Ferrari","Porsche","Jaguar","Land Rover","MINI","Bentley","Rolls-Royce","Lotus","Aston Martin","McLaren","Bugatti","Lancia","Saab","Opel","Seat","Skoda","Smart","Hummer","Pontiac","Saturn","Abarth","Ariel","Aston Martin","Bentley","Bristol","Caterham","Dax","Ferrari","Fiat","Ford","Ginetta","Jaguar","Lamborghini","Land Rover","Lotus","McLaren","Mercedes-Benz","MG","Morgan","Noble","Porsche","Rolls-Royce","TVR","Vauxhall","Volkswagen","Westfield"
        ];
        return $makes[rand(0,count($makes)-1)];
    }


}
