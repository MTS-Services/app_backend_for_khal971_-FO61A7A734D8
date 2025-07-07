<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserMilestoneAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->first();
         DB::table('user_milestone_achievements')->insert([
            [
                'user_id' => $user->id,
                'milestone_id' => 1,
                'progress_id' => 1,
                'achieved_value' => 100.00,
            ],
            [
                'user_id' => $user->id,
                'milestone_id' => 2,
                'progress_id' => 2,
                'achieved_value' => 90.00,
            ],
        ]);
    }
}
