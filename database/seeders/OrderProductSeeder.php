<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\OrderProduct;

class OrderProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderProduct::factory()->count(100)->create();
    }
}
