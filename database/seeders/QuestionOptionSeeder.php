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
        DB::table('question_options')->insert([
            [
                'order_index' => 101,
                'question_id' => 1,
                'option_text' => '3',
                'explanation' => '3 is too low.',
            ],
            [
                'order_index' => 102,
                'question_id' => 1,
                'option_text' => '4',
                'explanation' => 'Correct! 2 + 2 = 4.',
            ],
            [
                'order_index' => 103,
                'question_id' => 1,
                'option_text' => '5',
                'explanation' => '5 is too high.',
            ],
            [
                'order_index' => 104,
                'question_id' => 2,
                'option_text' => 'True',
                'explanation' => 'Incorrect. The Earth is round.',
            ],
            [
                'order_index' => 105,
                'question_id' => 2,
                'option_text' => 'False',
                'explanation' => 'Correct. The Earth is not flat.',
            ],
        ]);
    }
}
