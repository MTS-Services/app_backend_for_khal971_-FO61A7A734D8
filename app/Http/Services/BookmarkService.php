<?php

namespace App\Http\Services;

use App\Models\Bookmark;
use App\Models\Practice;
use App\Models\Question;
use App\Models\QuestionDetails;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

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
        $questions = Bookmark::where('user_id', $this->user->id)
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
            ])
            ->get();
        // ->get()
        // ->loadMorph('bookmarkable', [
        //     QuestionDetails::class => ['translations', 'practice','questions']
        // ]);

        return $questions;
    }
    public function getBookmarkedQuizzes()
    {
        $questions = Bookmark::where('user_id', $this->user->id)
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
            ])
            ->get();
        return $questions;
    }
}
