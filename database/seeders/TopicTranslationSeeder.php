<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('topic_translations')->insert([
            [
                'topic_id' => 1,
                'language' => 'en',
                'name' => 'Introduction to Variables',
            ],
            [
                'topic_id' => 2,
                'language' => 'en',
                'name' => 'Linear Equations',
            ],
            [
                'topic_id' => 3,
                'language' => 'en',
                'name' => 'Triangles & Angles',
            ],
        ]);
    }
}
