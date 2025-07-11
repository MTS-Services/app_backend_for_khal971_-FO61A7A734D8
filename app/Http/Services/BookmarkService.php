<?php

namespace App\Http\Services;

use App\Models\Bookmark;
use App\Models\QuestionDetails;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookmarkService
{
    private User $user;
    protected string $lang;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }

    public static function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }

    public function getBookmarkedQuestionDetails()
    {
        return Bookmark::where('user_id', $this->user->id)
            ->whereHasMorph('bookmarkable', [QuestionDetails::class])
            ->latest()
            ->with([
                'bookmarkable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        QuestionDetails::class => ['translations', 'practice'],
                    ])->morphWithCount([
                        QuestionDetails::class => ['questions'],
                    ]);
                }
            ])->get();
    }

    public function getBookmarkedQuizzes()
    {
        return Bookmark::where('user_id', $this->user->id)
            ->whereHasMorph('bookmarkable', [Quiz::class])
            ->latest()
            ->with([
                'bookmarkable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Quiz::class => ['translations', 'practice'],
                    ])->morphWithCount([
                        Quiz::class => ['options'],
                    ]);
                }
            ])->get();
    }

    public function addQuestionDetailsToBookmark($question_id): void
    {
        if (!QuestionDetails::find($question_id)) {
            throw new ModelNotFoundException('QuestionDetails not found.');
        }

        Bookmark::firstOrCreate([
            'user_id' => $this->user->id,
            'bookmarkable_id' => $question_id,
            'bookmarkable_type' => QuestionDetails::class,
        ]);
    }

    public function removeQuestionDetailsFromBookmark($question_id): void
    {
        $bookmark = Bookmark::where([
            'user_id' => $this->user->id,
            'bookmarkable_id' => $question_id,
            'bookmarkable_type' => QuestionDetails::class,
        ])->first();

        if (!$bookmark) {
            throw new ModelNotFoundException('Bookmark for QuestionDetails not found.');
        }

        $bookmark->delete();
    }

    public function addQuizToBookmark($quiz_id): void
    {
        if (!Quiz::find($quiz_id)) {
            throw new ModelNotFoundException('Quiz not found.');
        }

        Bookmark::firstOrCreate([
            'user_id' => $this->user->id,
            'bookmarkable_id' => $quiz_id,
            'bookmarkable_type' => Quiz::class,
        ]);
    }

    public function removeQuizFromBookmark($quiz_id): void
    {
        $bookmark = Bookmark::where([
            'user_id' => $this->user->id,
            'bookmarkable_id' => $quiz_id,
            'bookmarkable_type' => Quiz::class,
        ])->first();

        if (!$bookmark) {
            throw new ModelNotFoundException('Bookmark for Quiz not found.');
        }

        $bookmark->delete();
    }
}
