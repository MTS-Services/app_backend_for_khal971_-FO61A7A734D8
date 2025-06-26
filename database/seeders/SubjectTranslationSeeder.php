<?php

namespace Database\Seeders;

use App\Models\SubjectTranslation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubjectTranslation::create([
            'subject_id' => 1,
            'language' => 'es',
            'name' => 'Inglés'
        ]);
        SubjectTranslation::create([
            'subject_id' => 2,
            'language' => 'es',
            'name' => 'Matemáticas'
        ]);
        SubjectTranslation::create([
            'subject_id' => 3,
            'language' => 'es',
            'name' => 'Ciencia'
        ]);
        SubjectTranslation::create([
            'subject_id' => 1,
            'language' => 'it',
            'name' => 'Inglese'
        ]);
        SubjectTranslation::create([
            'subject_id' => 2,
            'language' => 'it',
            'name' => 'Matematica'
        ]);
        SubjectTranslation::create([
            'subject_id' => 3,
            'language' => 'it',
            'name' => 'Scienza'
        ]);
        SubjectTranslation::create([
            'subject_id' => 1,
            'language' => 'ar',
            'name' => 'اللغة الإنجليزية'
        ]);
        SubjectTranslation::create([
            'subject_id' => 2,
            'language' => 'ar',
            'name' => 'الرياضيات'
        ]);
        SubjectTranslation::create([
            'subject_id' => 3,
            'language' => 'ar',
            'name' => 'العلوم'
        ]);
    }
}
