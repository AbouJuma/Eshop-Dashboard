<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ProductRating;

class ProductRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductRating::factory()->count(100)->create();
    }
}
