<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProgressMilestone;
use App\Models\ProgressMilestoneTranslation;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgressMilestoneTranslation>
 */
class ProgressMilestoneTranslationFactory extends Factory
{
    protected $model = ProgressMilestoneTranslation::class;
    public function definition(): array
    {
        return [
           'progress_milestone_id' => ProgressMilestone::factory(),
            'language' => $this->faker->randomElement(['en', 'ar']),
            'content_type' => $this->faker->randomElement(ProgressMilestoneTranslation::getContentTypes()),
            'milestone_type' => $this->faker->randomElement(ProgressMilestoneTranslation::getMilestoneTypes()),
            'requirement_description' => $this->faker->sentence(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'celebration_message' => $this->faker->sentence(),
            'badge_name' => $this->faker->word(),
        ];
    }
}
