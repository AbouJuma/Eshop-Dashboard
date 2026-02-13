<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\VehicleModel;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VehicleModel::factory()->count(100)->create();
    }
}
