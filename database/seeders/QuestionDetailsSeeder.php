<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_details')->insert([
             [
                'topic_id'     => 1,
            ],
            [
                'topic_id'     => 1,
            ],
        ]);
    }
}
