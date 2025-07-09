<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserItemProgresss;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserItemProgressFactory extends Factory
{
    protected $model = UserItemProgresss::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'parent_progress_id' => UserProgress::factory(),
            'item_type' => $this->faker->randomElement(['question', 'lesson']),
            'item_id' => $this->faker->numberBetween(1, 1000),
            'item_order' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['not_started', 'viewed', 'attempted', 'completed', 'correct', 'incorrect']),
            'attempts' => $this->faker->numberBetween(0, 5),
            'correct_attempts' => $this->faker->numberBetween(0, 5),
            'time_spent' => $this->faker->numberBetween(10, 600),
            'first_accessed_at' => now()->subDays(rand(1, 10)),
            'last_accessed_at' => now(),
            'completed_at' => $this->faker->boolean ? now() : null,
            'score' => $this->faker->randomFloat(2, 0, 100),
            'is_bookmarked' => $this->faker->boolean,
            'is_flagged' => $this->faker->boolean,
            'notes' => $this->faker->sentence,
        ];
    }
}
