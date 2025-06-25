<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('question_options')->insert([
            [
                'question_id' => 1,
                'option_text' => '3',
                'is_correct' => false,
                'explanation' => '3 is too low.',
            ],
            [
                'question_id' => 1,
                'option_text' => '4',
                'is_correct' => true,
                'explanation' => 'Correct! 2 + 2 = 4.',
            ],
            [
                'question_id' => 1,
                'option_text' => '5',
                'is_correct' => false,
                'explanation' => '5 is too high.',
            ],
            [
                'question_id' => 2,
                'option_text' => 'True',
                'is_correct' => false,
                'explanation' => 'Incorrect. The Earth is round.',
            ],
            [
                'question_id' => 2,
                'option_text' => 'False',
                'is_correct' => true,
                'explanation' => 'Correct. The Earth is not flat.',
            ],
        ]);
    }
}
