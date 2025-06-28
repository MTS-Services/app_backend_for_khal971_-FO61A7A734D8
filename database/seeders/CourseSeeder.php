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

        DB::table('courses')->insert([
            [
                'subject_id' => 1,
            ],
            [
                'subject_id' => 1,
            ],
            [
                'subject_id' => 2,
            ],
        ]);
    }
}
