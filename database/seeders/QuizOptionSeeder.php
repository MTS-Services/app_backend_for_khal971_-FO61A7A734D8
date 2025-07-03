<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quiz_options')->insert([
            [
                'quiz_id' => 1,
            ],
            [
                'quiz_id' => 2,
            ],
            [
                'quiz_id' => 3,
            ],
            [
                'quiz_id' => 4,
            ],
            [
                'quiz_id' => 5,
            ],
        ]);
    }
}
