<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionDetailsTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_details_translations')->insert([
            [
                'question_detail_id' => 1,
                'language' => 'en',
                'title' => 'What is the capital of France?',
                'description' => 'This is a basic geography question.',
            ],
            [
                'question_detail_id' => 1,
                'language' => 'es',
                'title' => '¿Cuál es la capital de Francia?',
                'description' => 'Esta es una pregunta básica de geografía.',
            ],
            [
                'question_detail_id' => 1,
                'language' => 'ar',
                'title' => 'ما هي عاصمة فرنسا؟',
                'description' => 'هذا سؤال جغرافي أساسي.',
            ],
            [
                'question_detail_id' => 2,
                'language' => 'en',
                'title' => 'Explain the water cycle.',
                'description' => 'Briefly describe the stages.',
            ],
            [
                'question_detail_id' => 2,
                'language' => 'es',
                'title' => 'Explica el ciclo del agua.',
                'description' => 'Describe brevemente las etapas.',
            ],
            [
                'question_detail_id' => 2,
                'language' => 'ar',
                'title' => 'اشرح دورة الماء.',
                'description' => 'صف المراحل بإيجاز.',
            ],
        ]);
    }
}
