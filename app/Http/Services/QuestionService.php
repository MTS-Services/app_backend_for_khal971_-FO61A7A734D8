<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\Question;
use App\Models\QuestionTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionService
{
    private $user;
    protected string $lang;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
        $this->lang = request()->header('Accept-Language') ?: self::getDefaultLang();
    }
    public function getDefaultLang(): string
    {
        return defaultLang() ?: 'en';
    }
    public function getQuestions(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Question::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestion($param, string $query_field = 'id'): Question|null
    {
        $query = Question::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestion($data): Question|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question = Question::create($data);
                QuestionTranslation::create([
                    'question_id' => $question->id,
                    'language' => $this->lang,
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? '',
                    'point' => $data['point'] ?? 1,
                    'time_limit' => $data['time_limit'] ?? null,
                    'explanation' => $data['explanation'] ?? ''
                ]);
                TranslateModelJob::dispatch(Question::class, QuestionTranslation::class, 'question_id', $question->id, ['title', 'description', 'point', 'time_limit', 'explanation'], $this->lang);
                $question = $question->refresh()->loadTranslation($this->lang);
                return $question;
            });
        } catch (\Exception $e) {
            Log::error('Question Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuestion(Question $question, $data): Question|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            $question->update($data);
            QuestionTranslation::updateOrCreate(
                ['question_id' => $question->id, 'language' => $this->lang], // condition
                [
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? '',
                    'point' => $data['point'] ?? 1,
                    'time_limit' => $data['time_limit'] ?? null,
                    'explanation' => $data['explanation'] ?? ''
                ]
            );

            TranslateModelJob::dispatch(Question::class, QuestionTranslation::class, 'question_id', $question->id, ['title', 'description', 'point', 'time_limit', 'explanation'], $this->lang);
            $question = $question->refresh()->loadTranslation($this->lang);
            return $question;
        } catch (\Exception $e) {
            Log::error('Question Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuestion(Question $question): void
    {
        $question->delete();
    }
    public function toggleStatus(Question $question): Question
    {
        $question->update(['status' => !$question->status, 'updated_by' => $this->user->id]);
        return $question->refresh();
    }
}
