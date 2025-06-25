<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_types')->insert([
            [
                'order_index' => 101,
                'name' => 'mcq',
                'description' => 'Multiple Choice Questions',
            ],
            [
                'order_index' => 102,
                'name' => 'true_false',
                'description' => 'True or False Questions',
            ],
            [
                'order_index' => 103,
                'name' => 'short_answer',
                'description' => 'Short Answer Questions',
            ],
        ]);
    }
}
