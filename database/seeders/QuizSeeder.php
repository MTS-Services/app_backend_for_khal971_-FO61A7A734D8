<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quizzes')->insert([
            [
                'topic_id' => 2,
            ],
            [
                'topic_id' => 3,
            ],
            [
                'topic_id' => 1,
            ],
            [
                'topic_id' => 2,
            ],
            [
                'topic_id' => 3,
            ],
        ]);
    }
}
