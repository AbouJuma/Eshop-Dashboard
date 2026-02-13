<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vehicle::factory()->count(100)->create();
    }
}
