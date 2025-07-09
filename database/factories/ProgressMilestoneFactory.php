<?php

namespace Database\Factories;

use App\Models\ProgressMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgressMilestone>
 */
class ProgressMilestoneFactory extends Factory
{
    protected $model = ProgressMilestone::class;

    public function definition(): array
    {
        return [
            'content_type' => $this->faker->randomElement(['subject', 'course', 'topic']),
            'milestone_type' => $this->faker->randomElement(['completion', 'accuracy', 'streak']),
            'threshold_value' => $this->faker->randomFloat(2, 10, 100),
            'requirement_description' => $this->faker->sentence,
            'badge_name' => $this->faker->word,
            'badge_icon' => $this->faker->imageUrl(100, 100, 'badges'),
            'points_reward' => $this->faker->numberBetween(10, 100),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'celebration_message' => $this->faker->sentence,
            'is_active' => true,
            'order_index' => $this->faker->numberBetween(1, 50),
        ];
    }
}
