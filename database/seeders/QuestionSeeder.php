<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('questions')->insert([
            [
                'topic_id' => 1,
            ],
            [
                'topic_id' => 1,
            ]
        ]);
    }
}
