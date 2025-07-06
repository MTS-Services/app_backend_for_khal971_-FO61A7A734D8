<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('topics')->insert([
            [
                'course_id' => 1,
            ],
            [
                'course_id' => 1,
            ],
            [
                'course_id' => 2,
            ],
        ]);
    }
}
