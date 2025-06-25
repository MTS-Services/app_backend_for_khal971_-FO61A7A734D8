<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('topics')->insert([
            [
                'course_id' => 1,
                'name' => 'Introduction to Variables',
            ],
            [
                'course_id' => 1,
                'name' => 'Linear Equations',
            ],
            [
                'course_id' => 2,
                'name' => 'Triangles & Angles',
            ],
        ]);
    }
}
