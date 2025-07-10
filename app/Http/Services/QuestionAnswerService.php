<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionAnswerTranslation;
use App\Models\QuestionTranslation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionAnswerService
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
    public function getQuestionAnswers(int $question_id, string $orderBy = 'order_index', string $direction = 'asc')
    {
        $query = QuestionAnswer::with('translations')->where('question_id', $question_id);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestionAnswer($param, string $query_field = 'id'): QuestionAnswer|null
    {
        $query = QuestionAnswer::with('translations');
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestionAnswer(array $data): QuestionAnswer|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question_answer = QuestionAnswer::create($data);
                $correctAnswer = QuestionTranslation::where('question_id', $data['question_id'])->where('language', $this->lang)->first()->answer;
                $userAnswer = $data['answer'];
                similar_text(strtolower($correctAnswer), strtolower($userAnswer), $percent);
                if (empty($correctAnswer) || empty($userAnswer)) {
                    $percent = 0;
                }
                QuestionAnswerTranslation::create([
                    'question_answer_id' => $question_answer->id,
                    'language' => $this->lang,
                    'answer' => $data['answer'] ?? '',
                    'match_percentage' => round($percent),
                ]);
                TranslateModelJob::dispatch(QuestionAnswer::class, QuestionAnswerTranslation::class, 'question_answer_id', $question_answer->id, ['answer', 'match_percentage'], $this->lang);
                $question_answer = $question_answer->refresh()->load('translations');
                return $question_answer;
            });
        } catch (\Exception $e) {
            Log::error('QuestionAnswer Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuesitonAnswer(array $data, QuestionAnswer $question_answer): QuestionAnswer|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($data, $question_answer) {
                $question_answer->update($data);
                QuestionAnswerTranslation::where('question_answer_id', $question_answer->id)->where('language', $this->lang)->update([
                    'answer' => $data['answer'] ?? '',
                    'match_percentage' => $data['match_percentage'] ?? 0
                ]);
                TranslateModelJob::dispatch(QuestionAnswer::class, QuestionAnswerTranslation::class, 'question_answer_id', $question_answer->id, ['answer', 'match_percentage'], $this->lang);
                $question_answer = $question_answer->refresh()->load('translations');
                return $question_answer;
            });
        } catch (\Exception $e) {
            Log::error('QuestionAnswer Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuestionAnswer(QuestionAnswer $question_answer): void
    {
        $question_answer->delete();
    }
    public function toggleStatus(QuestionAnswer $question_answer): QuestionAnswer|null
    {
        $question_answer->update(['status' => !$question_answer->status, 'updated_by' => $this->user->id]);
        return $question_answer->refresh();
    }
}
