<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();
        DB::table('quiz_answers')->insert([
            [
                'quiz_id' => 1,
                'user_id' => $user->id
            ],
            [
                'quiz_id' => 2,
                'user_id' => $user->id
            ],
            [
                'quiz_id' => 3,
                'user_id' => $user->id
            ],
            [
                'quiz_id' => 4,
                'user_id' => $user->id
            ],
            [
                'quiz_id' => 5,
                'user_id' => $user->id
            ]
        ]);
    }
}
