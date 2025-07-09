<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('topics')->insert([
        //     [
        //         'course_id' => 1,
        //     ],
        //     [
        //         'course_id' => 1,
        //     ],
        //     [
        //         'course_id' => 2,
        //     ],
        // ]);

        Topic::factory()->count(10)->create();
    }
}
