<?php

namespace Database\Factories;

use App\Models\QuestionDetails;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usedTopicIds = QuestionDetails::pluck('topic_id')->unique()->toArray();

        $topic = Topic::whereNotIn('id', $usedTopicIds)->inRandomOrder()->first();

        $topicId = $topic->id ?? null;

        if (is_null($topicId)) {          
            $topicId = Topic::inRandomOrder()->first()->id ?? 1;
        }

        return [
            'topic_id' => $topicId,            
        ];
    }
}
