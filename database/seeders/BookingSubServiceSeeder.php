<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\BookingSubService;

class BookingSubServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BookingSubService::factory()->count(100)->create();
    }
}
