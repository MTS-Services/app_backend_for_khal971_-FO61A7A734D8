<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();
        if ($user) {
            foreach ([1, 2, 3] as $subjectId) {
                DB::table('user_subjects')->insert([
                    'user_id' => $user->id,
                    'subject_id' => $subjectId,
                ]);
            }
        }
    }
}
