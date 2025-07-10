<?php

namespace App\Http\Services;

use App\Models\Practice;
use App\Models\QuestionDetails;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class PracticeService
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

    public function getQuestionDetails()
    {
        $questions = Practice::where('user_id', $this->user->id)
            ->whereHasMorph('practiceable', [QuestionDetails::class])
            ->with([
                'practiceable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        QuestionDetails::class => ['translations', 'practice'],
                    ])->morphWithCount([
                        QuestionDetails::class => ['questions'],
                    ]);
                }
            ])
            ->get();
        return $questions;
    }


    public function getQuizzes()
    {
        $quizzes = Practice::where('user_id', $this->user->id)
            ->whereHasMorph('practiceable', [Quiz::class])
            ->with([
                'practiceable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Quiz::class => ['translations', 'practice'],
                    ])->morphWithCount([
                        Quiz::class => ['options'],
                    ]);
                }
            ])
            ->get();
        return $quizzes;
    }

    public function getTopics()
    {
        $topics = Practice::where('user_id', $this->user->id)
            ->where('practiceable_type', 'App\Models\Topic')
            ->get();
        return $topics;
    }

    public function getCourses()
    {
        $courses = Practice::where('user_id', $this->user->id)
            ->where('practiceable_type', 'App\Models\Course')
            ->get();
        return $courses;
    }
}
