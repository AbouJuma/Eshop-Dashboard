<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Make;

class MakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Make::factory()->count(100)->create();
    }
}
