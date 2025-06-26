<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_options')->insert([
            [
                'question_id' => 1,
                'option_text' => '3',
                'explanation' => '3 is too low.',
            ],
            [
                'question_id' => 1,
                'option_text' => '4',
                'explanation' => 'Correct! 2 + 2 = 4.',
            ],
            [
                'question_id' => 1,
                'option_text' => '5',
                'explanation' => '5 is too high.',
            ],
            [
                'question_id' => 2,
                'option_text' => 'True',
                'explanation' => 'Incorrect. The Earth is round.',
            ],
            [
                'question_id' => 2,
                'option_text' => 'False',
                'explanation' => 'Correct. The Earth is not flat.',
            ],
        ]);
    }
}
