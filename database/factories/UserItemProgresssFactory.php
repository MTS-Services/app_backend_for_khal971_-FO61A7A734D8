<?php
namespace Database\Factories;

use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserItemProgresss;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserItemProgresssFactory extends Factory
{
    protected $model = UserItemProgresss::class;

    public function definition(): array
    {
        static $used = [];

        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $progress = UserProgress::inRandomOrder()
            ->where('user_id', $user->id)
            ->first()
            ?? UserProgress::factory()->create(['user_id' => $user->id]);

        $tries = 0;
        do {
            $itemType = $this->faker->randomElement(['question', 'lesson']);
            $itemId = $this->faker->numberBetween(1, 1000);
            $key = "{$user->id}-{$itemType}-{$itemId}";
            $tries++;
        } while (isset($used[$key]) && $tries < 20);

        // Mark as used
        $used[$key] = true;

        return [
            'user_id' => $user->id,
            'parent_progress_id' => $progress->id,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_order' => $this->faker->numberBetween(1, 20),
            'status' => array_rand(UserItemProgresss::getStatusList()),
            'attempts' => $this->faker->numberBetween(0, 5),
            'correct_attempts' => $this->faker->numberBetween(0, 5),
            'time_spent' => $this->faker->numberBetween(10, 600),
            'first_accessed_at' => now()->subDays(rand(1, 10)),
            'last_accessed_at' => now(),
            'completed_at' => $this->faker->boolean ? now() : null,
            'score' => $this->faker->randomFloat(2, 0, 100),
            'is_bookmarked' => $this->faker->boolean,
            'is_flagged' => $this->faker->boolean,
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
