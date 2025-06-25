<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_types')->insert([
            [
                'name' => 'mcq',
                'description' => 'Multiple Choice Questions',
            ],
            [
                'name' => 'true_false',
                'description' => 'True or False Questions',
            ],
            [
                'name' => 'short_answer',
                'description' => 'Short Answer Questions',
            ],
        ]);
    }
}
