<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('courses')->insert([
            [
                'subject_id' => 1,
                'name' => 'Algebra Basics',
            ],
            [
                'subject_id' => 1,
                'name' => 'Geometry Fundamentals',
            ],
            [
                'subject_id' => 2,
                'name' => 'Introduction to Physics',
            ],
        ]);
    }
}
