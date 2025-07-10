<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Practice;
use App\Models\QuestionDetails;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class PracticeFactory extends Factory
{
    public function definition(): array
    {
        $practiceableTypes = [
            Course::class,
            Topic::class,
            Quiz::class,
            QuestionDetails::class,
        ];

        $uniqueCombinationFound = false;
        $userId = null;
        $practiceableId = null;
        $selectedType = null;

        while (!$uniqueCombinationFound) {
            $selectedType = $this->faker->randomElement($practiceableTypes);

            $practiceableId = $selectedType::inRandomOrder()->first()->id ?? null;

            if (is_null($practiceableId)) {
                $practiceableId = Course::inRandomOrder()->first()->id ?? 1;
                $selectedType = Course::class;
            }

            $userId = User::inRandomOrder()->first()->id;

            $existingPractice = Practice::where('user_id', $userId)
                ->where('practiceable_id', $practiceableId)
                ->where('practiceable_type', $selectedType)
                ->first();

            if (is_null($existingPractice)) {
                $uniqueCombinationFound = true;
            }
        }

        return [
            'user_id' => $userId,
            'practiceable_id' => $practiceableId,
            'practiceable_type' => $selectedType,
            'total_attempts' => ($selectedType == Topic::class  || $selectedType == Course::class) ? 0 : $this->faker->numberBetween(1, 5),
        ];
    }
}
