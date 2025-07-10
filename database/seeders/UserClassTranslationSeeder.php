<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserClassTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
    1 => ['en' => 'One',       'es' => 'Uno',       'ar' => 'الأول'],
    2 => ['en' => 'Two',       'es' => 'Dos',       'ar' => 'الثاني'],
    3 => ['en' => 'Three',     'es' => 'Tres',      'ar' => 'الثالث'],
    4 => ['en' => 'Four',      'es' => 'Cuatro',    'ar' => 'الرابع'],
    5 => ['en' => 'Five',      'es' => 'Cinco',     'ar' => 'الخامس'],
    6 => ['en' => 'Six',       'es' => 'Seis',      'ar' => 'السادس'],
    7 => ['en' => 'Seven',     'es' => 'Siete',     'ar' => 'السابع'],
    8 => ['en' => 'Eight',     'es' => 'Ocho',      'ar' => 'الثامن'],
    9 => ['en' => 'Nine',      'es' => 'Nueve',     'ar' => 'التاسع'],
    10 => ['en' => 'Ten',      'es' => 'Diez',      'ar' => 'العاشر'],
    11 => ['en' => 'Eleven',   'es' => 'Once',      'ar' => 'الحادي عشر'],
    12 => ['en' => 'Twelve',   'es' => 'Doce',      'ar' => 'الثاني عشر'],
];

$translations = [];

foreach ($classes as $classId => $names) {
    foreach ($names as $lang => $name) {
        $translations[] = [
            'user_class_id' => $classId,
            'language' => $lang,
            'name' => $name,
        ];
    }
}

DB::table('user_class_translations')->insert($translations);
    }
}
