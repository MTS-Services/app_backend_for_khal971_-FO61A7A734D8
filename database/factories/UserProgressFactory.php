<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProgress>
 */
class UserProgressFactory extends Factory
{

    protected $model = UserProgress::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->numberBetween(5, 20);
        $completed = $this->faker->numberBetween(0, $total);
        $correct = $this->faker->numberBetween(0, $completed);

        // Ensure unique combination by caching
        static $used = [];

        $maxTries = 20;
        do {
            $userId = User::inRandomOrder()->value('id') ?? User::factory()->create()->id;
            $contentType = $this->faker->randomElement(['question', 'quiz', 'topic']);
            $contentId = $this->faker->numberBetween(1, 500);
            $key = "$userId-$contentType-$contentId";
            $maxTries--;
        } while (isset($used[$key]) && $maxTries > 0);

        $used[$key] = true;

        return [
            'user_id' => $userId,
            'content_type' => $contentType,
            'content_id' => $contentId,
            'total_items' => $total,
            'completed_items' => $completed,
            'correct_items' => $correct,
            'completion_percentage' => round(($completed / $total) * 100, 2),
            'accuracy_percentage' => $completed > 0 ? round(($correct / $completed) * 100, 2) : 0,
            'total_time_spent' => $this->faker->numberBetween(100, 10000),
            'average_time_per_item' => $total > 0 ? round($this->faker->numberBetween(10, 600), 2) : 0,
            'status' => array_rand(UserProgress::getStatusList()),
            'first_accessed_at' => now()->subDays(rand(1, 30)),
            'last_accessed_at' => now(),
            'completed_at' => $this->faker->boolean ? now() : null,
            'current_streak' => $this->faker->numberBetween(0, 10),
            'best_streak' => $this->faker->numberBetween(5, 20),
            'last_activity_date' => now()->subDays(rand(0, 10)),
        ];
    }
}
