<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $translations = [
            // Answer 1
            [
                'question_answer_id' => 1,
                'language' => 'en',
                'answer' => 'The capital of France is Paris.'
            ],
            [
                'question_answer_id' => 1,
                'language' => 'ar',
                'answer' => 'عاصمة فرنسا هي باريس.'
            ],
            [
                'question_answer_id' => 1,
                'language' => 'es',
                'answer' => 'La capital de Francia es París.'
            ],

            // Answer 2
            [
                'question_answer_id' => 2,
                'language' => 'en',
                'answer' => 'True'
            ],
            [
                'question_answer_id' => 2,
                'language' => 'ar',
                'answer' => 'صحيح'
            ],
            [
                'question_answer_id' => 2,
                'language' => 'es',
                'answer' => 'Verdadero'
            ],

            // Answer 3
            [
                'question_answer_id' => 3,
                'language' => 'en',
                'answer' => 'Water boils at 100°C.'
            ],
            [
                'question_answer_id' => 3,
                'language' => 'ar',
                'answer' => 'يغلي الماء عند 100 درجة مئوية.'
            ],
            [
                'question_answer_id' => 3,
                'language' => 'es',
                'answer' => 'El agua hierve a 100°C.'
            ],

            // Answer 4
            [
                'question_answer_id' => 4,
                'language' => 'en',
                'answer' => 'The largest planet is Jupiter.'
            ],
            [
                'question_answer_id' => 4,
                'language' => 'ar',
                'answer' => 'أكبر كوكب هو المشتري.'
            ],
            [
                'question_answer_id' => 4,
                'language' => 'es',
                'answer' => 'El planeta más grande es Júpiter.'
            ],

            // Answer 5
            [
                'question_answer_id' => 5,
                'language' => 'en',
                'answer' => 'Photosynthesis happens in leaves.'
            ],
            [
                'question_answer_id' => 5,
                'language' => 'ar',
                'answer' => 'التمثيل الضوئي يحدث في الأوراق.'
            ],
            [
                'question_answer_id' => 5,
                'language' => 'es',
                'answer' => 'La fotosíntesis ocurre en las hojas.'
            ],
        ];

        foreach ($translations as $row) {
            DB::table('question_answer_translations')->insert([
                'question_answer_id' => $row['question_answer_id'],
                'language' => $row['language'],
                'answer' => $row['answer'],
                'match_percentage' => rand(80, 100),
            ]);
        }
    }
}
