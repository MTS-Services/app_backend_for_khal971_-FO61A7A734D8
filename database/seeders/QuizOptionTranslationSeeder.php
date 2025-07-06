<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizOptionTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        $options = [
            1 => ['en' => 'Option A', 'es' => 'Opción A', 'ar' => 'الخيار أ'],
            2 => ['en' => 'Option B', 'es' => 'Opción B', 'ar' => 'الخيار ب'],
            3 => ['en' => 'Option C', 'es' => 'Opción C', 'ar' => 'الخيار ج'],
            4 => ['en' => 'Option D', 'es' => 'Opción D', 'ar' => 'الخيار د'],
            5 => ['en' => 'Option E', 'es' => 'Opción E', 'ar' => 'الخيار هـ'],
        ];

        foreach ($options as $optionId => $translations) {
            foreach ($translations as $lang => $title) {
                $data[] = [
                    'quiz_option_id' => $optionId,
                    'language' => $lang,
                    'title' => $title,
                ];
            }
        }

        DB::table('quiz_option_translations')->insert($data);
    }
}
