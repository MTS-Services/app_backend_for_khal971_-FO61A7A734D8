<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('question_options')->insert(
            array_merge(
                array_fill(0, 5, ['question_id' => 1]),
                array_fill(0, 4, ['question_id' => 2])
            )
        );
    }
}
