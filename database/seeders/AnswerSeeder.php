<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Answer;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Answer::factory()->count(100)->create();
    }
}
