<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $users = DB::table('users')->first();
        $progressRecords = [
            [
                'user_id' => $users->id,
                'content_type' => 'subject',
                'content_id' => 1,
                'total_items' => 10,
                'completed_items' => 5,
                'correct_items' => 4,
                'completion_percentage' => 50.00,
                'accuracy_percentage' => 80.00,
                'total_time_spent' => 600,
                'average_time_per_item' => 120,
                'first_accessed_at' => $now->copy()->subDays(3),
                'last_accessed_at' => $now,
                'current_streak' => 2,
                'best_streak' => 3,
                'last_activity_date' => $now->toDateString(),
            ],
            [
                'user_id' => $users->id,
                'content_type' => 'course',
                'content_id' => 2,
                'total_items' => 15,
                'completed_items' => 15,
                'correct_items' => 14,
                'completion_percentage' => 100.00,
                'accuracy_percentage' => 93.33,
                'total_time_spent' => 1800,
                'average_time_per_item' => 120,
                'first_accessed_at' => $now->copy()->subDays(5),
                'last_accessed_at' => $now->copy()->subDay(),
                'current_streak' => 0,
                'best_streak' => 4,
                'last_activity_date' => $now->copy()->subDay()->toDateString()
            ],
            [
                'user_id' => $users->id,
                'content_type' => 'topic',
                'content_id' => 3,
                'total_items' => 8,
                'completed_items' => 3,
                'correct_items' => 2,
                'completion_percentage' => 37.50,
                'accuracy_percentage' => 66.67,
                'total_time_spent' => 480,
                'average_time_per_item' => 160,
                'first_accessed_at' => $now->copy()->subDays(1),
                'last_accessed_at' => $now,
                'current_streak' => 1,
                'best_streak' => 2,
                'last_activity_date' => $now->toDateString(),
            ],
            [
                'user_id' => $users->id,
                'content_type' => 'quiz',
                'content_id' => 1,
                'total_items' => 5,
                'completed_items' => 5,
                'correct_items' => 5,
                'completion_percentage' => 100.00,
                'accuracy_percentage' => 100.00,
                'total_time_spent' => 300,
                'average_time_per_item' => 60,
                'first_accessed_at' => $now->copy()->subDays(2),
                'last_accessed_at' => $now,
                'current_streak' => 3,
                'best_streak' => 3,
                'last_activity_date' => $now->toDateString(),
            ],
            [
                'user_id' => $users->id,
                'content_type' => 'question_set',
                'content_id' => 7,
                'total_items' => 20,
                'completed_items' => 0,
                'correct_items' => 0,
                'completion_percentage' => 0.00,
                'accuracy_percentage' => 0.00,
                'total_time_spent' => 0,
                'average_time_per_item' => 0,
                'first_accessed_at' => null,
                'last_accessed_at' => null,
                'current_streak' => 0,
                'best_streak' => 0,
                'last_activity_date' => null,
            ],
        ];

        DB::table('user_progress')->insert($progressRecords);
    }
}
