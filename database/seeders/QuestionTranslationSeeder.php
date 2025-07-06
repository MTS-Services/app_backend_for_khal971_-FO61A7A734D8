<?php

namespace Database\Seeders;

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
            // Question 1: Solar system question
            [
                'question_id' => 1,
                'language' => 'en',
                'title' => 'Which planet is known as the Red Planet?',
                'answer' => 'Mars is known as the Red Planet.',
            ],
            [
                'question_id' => 1,
                'language' => 'es',
                'title' => '¿Qué planeta se conoce como el Planeta Rojo?',
                'answer' => 'Marte es conocido como el Planeta Rojo.',
            ],
            [
                'question_id' => 1,
                'language' => 'ar',
                'title' => 'أي كوكب يُعرف بالكوكب الأحمر؟',
                'answer' => 'يُعرف المريخ بالكوكب الأحمر.',
            ],

            // Question 2: History question
            [
                'question_id' => 2,
                'language' => 'en',
                'title' => 'Who was the first President of the United States?',
                'answer' => 'George Washington was the first U.S. President.',
            ],
            [
                'question_id' => 2,
                'language' => 'es',
                'title' => '¿Quién fue el primer presidente de los Estados Unidos?',
                'answer' => 'George Washington fue el primer presidente de EE.UU.',
            ],
            [
                'question_id' => 2,
                'language' => 'ar',
                'title' => 'من كان أول رئيس للولايات المتحدة؟',
                'answer' => 'كان جورج واشنطن أول رئيس للولايات المتحدة.',
            ],
        ]);
    }
}
