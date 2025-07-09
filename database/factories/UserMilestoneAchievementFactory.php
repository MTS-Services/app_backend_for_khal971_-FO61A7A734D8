<?php

namespace Database\Factories;

use App\Models\ProgressMilestone;
use App\Models\User;
use App\Models\UserMilestoneAchievement;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMilestoneAchievement>
 */
class UserMilestoneAchievementFactory extends Factory
{
    protected $model = UserMilestoneAchievement::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'milestone_id' => ProgressMilestone::factory(),
            'progress_id' => UserProgress::factory(),
            'achieved_value' => $this->faker->randomFloat(2, 10, 100),
            'achieved_at' => now()->subDays(rand(1, 15)),
            'is_notified' => $this->faker->boolean,
            'notification_sent_at' => now(),
        ];
    }
}
