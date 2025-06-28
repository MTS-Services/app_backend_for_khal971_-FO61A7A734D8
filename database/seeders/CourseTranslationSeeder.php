<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('course_translations')->insert([
            [
                'course_id' => "1",
                'language' => 'en',
                'name' => 'Algebra Basics',
            ],
            [
                'course_id' => "2",
                'language' => 'en',
                'name' => 'Geometry Fundamentals',
            ],
            [
                'course_id' => "3",
                'language' => 'en',
                'name' => 'Introduction to Physics',
            ],
        ]);
    }
}
