<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('question_translations')->insert([
            [
                'question_id' => 1,
                'language' => 'en',
                'title' => 'What is 2 + 2?',
                'description' => 'Basic math addition question.',
                'point' => 1,
                'time_limit' => 30,
                'explanation' => '2 plus 2 equals 4.',
            ],
            [
                'question_id' => 2,
                'language' => 'en',
                'title' => 'The Earth is flat. (True/False)',
                'description' => 'Geography question.',
                'point' => 1,
                'time_limit' => null,
                'explanation' => 'The Earth is round, not flat.',
            ]
        ]);
    }
}
