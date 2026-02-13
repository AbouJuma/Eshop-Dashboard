<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\MfgYear;

class MfgYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MfgYear::factory()->count(10)->create();
    }
}
