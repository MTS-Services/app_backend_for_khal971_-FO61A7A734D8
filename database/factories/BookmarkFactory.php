<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\QuestionDetails;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bookmark>
 */
class BookmarkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookmarkableTypes = [
            Quiz::class,
            QuestionDetails::class,
        ];

        $uniqueCombinationFound = false;
        $userId = null;
        $bookmarkableId = null;
        $selectedType = null;

        while (!$uniqueCombinationFound) {
            $selectedType = $this->faker->randomElement($bookmarkableTypes);

            $bookmarkableId = $selectedType::inRandomOrder()->first()->id ?? null;
            if (is_null($bookmarkableId)) {
                $bookmarkableId = Quiz::inRandomOrder()->first()->id ?? QuestionDetails::inRandomOrder()->first()->id ?? 1;
                $selectedType = Quiz::class;
            }

            $userId = User::inRandomOrder()->first()->id;

            $existingBookmark = Bookmark::where('user_id', $userId)
                ->where('bookmarkable_id', $bookmarkableId)
                ->where('bookmarkable_type', $selectedType)
                ->first();

            if (is_null($existingBookmark)) {
                $uniqueCombinationFound = true;
            }
        }

        return [
            'user_id' => $userId,
            'bookmarkable_id' => $bookmarkableId,
            'bookmarkable_type' => $selectedType,
        ];
    }
}
