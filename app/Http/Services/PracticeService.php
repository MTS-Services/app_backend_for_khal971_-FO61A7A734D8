<?php

namespace App\Http\Services;

use App\Models\Practice;
use App\Models\User;
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

    public function getQuizzes()
    {
        $quizzes = Practice::where('user_id', $this->user->id)
            ->where('practiceable_type', 'App\Models\Quiz')
            ->get();
        return $quizzes;
    }

    public function getQuestions()
    {
        $questions = Practice::where('user_id', $this->user->id)
            ->where('practiceable_type', 'App\Models\Question')
            ->get();
        return $questions;
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
