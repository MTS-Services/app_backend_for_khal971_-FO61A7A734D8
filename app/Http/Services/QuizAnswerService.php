<?php

namespace App\Http\Services;

use App\Models\QuizAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizAnswerService
{
    private User $user;
    protected string $lang;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }


    public static function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    public function getQuizAnswers( int $quiz_id, string $orderBy = 'order_index', string $direction = 'asc')
    {
        $query = QuizAnswer::where('quiz_id', $quiz_id)->with(['quiz','user','quizOption']);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuizAnswer($param, string $query_field = 'id'): QuizAnswer|null
    {
        $query = QuizAnswer::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuizAnswer(array $data): QuizAnswer|null
    {
        try {
            $data['created_by'] = $this->user->id;
            $data['user_id'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question_answer = QuizAnswer::create($data);
                $question_answer = $question_answer->refresh();
                return $question_answer;
            });
        } catch (\Exception $e) {
            Log::error('QuizAnswer Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuesitonAnswer(array $data, QuizAnswer $question_answer): QuizAnswer|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            $data['user_id'] = $this->user->id;
            return DB::transaction(function () use ($data, $question_answer) {
                $question_answer->update($data);
                $question_answer = $question_answer->refresh();
                return $question_answer;
            });
        } catch (\Exception $e) {
            Log::error('QuizAnswer Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuizAnswer(QuizAnswer $question_answer): void
    {
        $question_answer->delete();
    }
    public function toggleStatus(QuizAnswer $question_answer): QuizAnswer|null
    {
        $question_answer->update(['status' => !$question_answer->status, 'updated_by' => $this->user->id]);
        return $question_answer->refresh();
    }
}
