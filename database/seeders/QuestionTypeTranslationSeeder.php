<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_type_translations')->insert([
            [
                'question_type_id' => 1,
                'language' => 'en',
                'name' => 'mcq',
                'description' => 'Multiple Choice Questions',
            ],
            [
                'question_type_id' => 2,
                'language' => 'en',
                'name' => 'true_false',
                'description' => 'True or False Questions',
            ],
            [
                'question_type_id' => 3,
                'language' => 'en',
                'name' => 'short_answer',
                'description' => 'Short Answer Questions',
            ],
        ]);
    }
}
