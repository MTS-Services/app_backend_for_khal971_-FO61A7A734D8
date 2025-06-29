<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionOptionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            // Question Option ID 1
            [
                'question_option_id' => 1,
                'language' => 'en',
                'option_text' => 'True',
                'explanation' => 'This statement is correct.',
            ],
            [
                'question_option_id' => 1,
                'language' => 'es',
                'option_text' => 'Verdadero',
                'explanation' => 'Esta afirmación es correcta.',
            ],
            [
                'question_option_id' => 1,
                'language' => 'ar',
                'option_text' => 'صحيح',
                'explanation' => 'هذا البيان صحيح.',
            ],

            // Question Option ID 2
            [
                'question_option_id' => 2,
                'language' => 'en',
                'option_text' => 'False',
                'explanation' => 'This statement is incorrect.',
            ],
            [
                'question_option_id' => 2,
                'language' => 'es',
                'option_text' => 'Falso',
                'explanation' => 'Esta afirmación es incorrecta.',
            ],
            [
                'question_option_id' => 2,
                'language' => 'ar',
                'option_text' => 'خطأ',
                'explanation' => 'هذا البيان غير صحيح.',
            ],

            // Question Option ID 3
            [
                'question_option_id' => 3,
                'language' => 'en',
                'option_text' => 'Paris',
                'explanation' => 'Paris is the capital of France.',
            ],
            [
                'question_option_id' => 3,
                'language' => 'es',
                'option_text' => 'París',
                'explanation' => 'París es la capital de Francia.',
            ],
            [
                'question_option_id' => 3,
                'language' => 'ar',
                'option_text' => 'باريس',
                'explanation' => 'باريس هي عاصمة فرنسا.',
            ],
        ];

        DB::table('question_option_translations')->insert($translations);
    }
}
