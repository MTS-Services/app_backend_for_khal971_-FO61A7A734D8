<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quizzes_translations')->insert([
            [
                'quiz_id' => 1,
                'language' => 'en',
                'title' => 'General Knowledge',
                'description' => 'A  Quiz on general topics.',
            ],
            [
                'quiz_id' => 1,
                'language' => 'es',
                'title' => 'Conocimientos Generales',
                'description' => 'Un cuestionario sobre temas generales.',
            ],
            [
                'quiz_id' => 1,
                'language' => 'ar',
                'title' => 'المعرفة العامة',
                'description' => 'اختبار في المواضيع العامة.',
            ],
        ]);
    }
}
