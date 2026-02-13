<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\SubService;

class SubServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubService::factory()->count(50)->create();
    }
}
