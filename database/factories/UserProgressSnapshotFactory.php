<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProgressSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProgressSnapshot>
 */
class UserProgressSnapshotFactory extends Factory
{
    protected $model = UserProgressSnapshot::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'snapshot_date' => now()->subDays(rand(1, 30)),
            'snapshot_type' => $this->faker->randomElement(['daily', 'weekly']),
            'total_subjects' => $this->faker->numberBetween(1, 5),
            'active_subjects' => $this->faker->numberBetween(1, 5),
            'completed_subjects' => $this->faker->numberBetween(0, 5),
            'total_courses' => $this->faker->numberBetween(1, 10),
            'active_courses' => $this->faker->numberBetween(1, 10),
            'completed_courses' => $this->faker->numberBetween(0, 10),
            'total_topics' => $this->faker->numberBetween(1, 50),
            'completed_topics' => $this->faker->numberBetween(0, 50),
            'total_questions_attempted' => $this->faker->numberBetween(0, 100),
            'total_questions_correct' => $this->faker->numberBetween(0, 100),
            'overall_completion_percentage' => $this->faker->randomFloat(2, 0, 100),
            'overall_accuracy_percentage' => $this->faker->randomFloat(2, 0, 100),
            'total_time_spent' => $this->faker->numberBetween(1000, 10000),
            'current_streak' => $this->faker->numberBetween(1, 10),
            'longest_streak' => $this->faker->numberBetween(5, 20),
        ];
    }
}
