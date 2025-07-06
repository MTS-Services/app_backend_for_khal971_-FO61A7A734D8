<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressMilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('progress_milestones')->insert([
            [
                'threshold_value' => 100.00,
                'badge_icon' => 'icons/badges/subject_master.svg',
                'points_reward' => 50,
            ],
            [
                'threshold_value' => 90.00,
                'badge_icon' => 'icons/badges/quiz_ace.svg',
                'points_reward' => 30,
            ],
            [
                'threshold_value' => 7.00,
                'badge_icon' => 'icons/badges/weekly_warrior.svg',
                'points_reward' => 70,
            ],
        ]);
    }
}
