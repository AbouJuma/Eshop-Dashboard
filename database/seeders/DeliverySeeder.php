<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Delivery;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Delivery::factory()->count(100)->create();
    }
}
