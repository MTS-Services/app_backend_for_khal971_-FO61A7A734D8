<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $now = Carbon::now();

        DB::table('questions')->insert([
            [
                'order_index' => 101,
                'topic_id' => 1,
                'question_type_id' => 1,
                'title' => 'What is 2 + 2?',
                'description' => 'Basic math addition question.',
                'file' => null,
                'points' => 1,
                'time_limit' => 30,
                'explanation' => '2 plus 2 equals 4.',
                'hints' => json_encode(['Try using your fingers', 'It’s a simple sum']),
                'tags' => json_encode(['math', 'easy', 'grade1']),
                'status' => Question::STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'order_index' => 102,
                'topic_id' => 1,
                'question_type_id' => 2,
                'title' => 'The Earth is flat. (True/False)',
                'description' => null,
                'file' => null,
                'points' => 1,
                'time_limit' => null,
                'explanation' => 'The Earth is round, not flat.',
                'hints' => json_encode(['Think of a globe']),
                'tags' => json_encode(['science', 'grade1']),
                'status' => Question::STATUS_INACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
