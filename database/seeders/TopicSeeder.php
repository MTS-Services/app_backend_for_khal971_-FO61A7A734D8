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
                'order_index' => 101,
                'course_id' => 1,
                'name' => 'Introduction to Variables',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order_index' => 102,
                'course_id' => 1,
                'name' => 'Linear Equations',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'order_index' => 103,
                'course_id' => 2,
                'name' => 'Triangles & Angles',
                'status' => 'inactive',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => null,
            ],
        ]);
    }
}
