<?php

namespace App\Http\Services;

use App\Models\Bookmark;
use App\Models\Practice;
use App\Models\Question;
use App\Models\QuestionDetails;
use App\Models\User;
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

    public function getBookmarkedQuestions()
    {
        $questions = Bookmark::where('user_id', $this->user->id)
            ->whereHasMorph('bookmarkable', [QuestionDetails::class])
            ->latest()
            ->get()
            ->loadMorph('bookmarkable', [
                QuestionDetails::class => ['translations', 'practices']
            ]);

        return $questions;
    }
    public function getBookmarkedQuizzes()
    {
        $questions = Bookmark::where('user_id', $this->user->id)
            ->where('bookmarkable_type', 'App\Models\Quiz')
            ->get();
        return $questions;
    }
}
