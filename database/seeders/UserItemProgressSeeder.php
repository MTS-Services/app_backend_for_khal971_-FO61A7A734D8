<?php

namespace Database\Seeders;

use App\Models\UserItemProgresss;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserItemProgressSeeder extends Seeder
{
    public function run(): void
    {

        UserItemProgresss::factory(100)->create();

        // $now = Carbon::now();
        // $user = DB::table('users')->first();

        // if (!$user) {
        //     $this->command->warn('No users found in the database. Seeder skipped.');
        //     return;
        // }

        // $progressData = [];

        // for ($i = 1; $i <= 5; $i++) {
        //     $progressData[] = [
        //         'user_id' => $user->id,
        //         'parent_progress_id' => 1,
        //         'item_type' => 'question',
        //         'item_id' => $i,
        //         'item_order' => $i,
        //         'status' => 1, // Assuming STATUS_COMPLETED = 1
        //         'time_spent' => rand(30, 300),
        //         'first_accessed_at' => $now->copy()->subDays(2)->toDateString(),
        //         'last_accessed_at' => $now->toDateString(),
        //         'completed_at' => $now->toDateString(),
        //         'score' => rand(60, 100),
        //         'notes' => 'Auto-generated progress data',
        //     ];
        // }

        // DB::table('user_item_progress')->insert($progressData);
    }
}
