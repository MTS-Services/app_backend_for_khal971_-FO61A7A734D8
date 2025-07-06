<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();
        if ($user) {
                DB::table('question_answers')->insert([
                    [
                        'question_id' => 1,
                        'user_id' => $user->id,
                    ],
                    [
                        'question_id' => 2,
                        'user_id' => $user->id,
                    ],
                    [
                        'question_id' => 2,
                        'user_id' => $user->id,
                    ],
                    [
                        'question_id' => 1,
                        'user_id' => $user->id,
                    ],
                    [
                        'question_id' => 2,
                        'user_id' => $user->id,
                    ],
                ]);
        }
    }
}
