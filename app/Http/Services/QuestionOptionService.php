<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\QuestionOption;
use App\Models\QuestionOptionTranslation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionOptionService
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


    /**
     * Fetch Questions, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getQuestionOptions(string $orderBy = 'order_index', string $direction = 'asc')
    {

        $query = QuestionOption::translation($this->lang);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getQuestionOption($param, string $query_field = 'id'): QuestionOption|null
    {
        $query = QuestionOption::translation($this->lang);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free();
        }
        $question_option = $query->where($query_field, $param)->first();
        return $question_option;
    }
    public function createQuestionOption($data): QuestionOption|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question_option = QuestionOption::create($data);
                QuestionOptionTranslation::create(['question_option_id' => $question_option->id, 'language' => $this->lang, 'option_text' => $data['option_text'], 'explanation' => $data['explanation']]);
                TranslateModelJob::dispatch(QuestionOption::class, QuestionOptionTranslation::class, 'question_option_id', $question_option->id, ['option_text', 'explanation'], $this->lang);
                $question_option = $question_option->refresh()->loadTranslation($this->lang);
                return $question_option;
            });
        } catch (\Exception $e) {
            Log::error('Question Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateQuestionOption(QuestionOption $question_option, $data): QuestionOption|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($question_option, $data) {
                $question_option->update($data);
                QuestionOptionTranslation::updateOrCreate(
                    [
                        'question_option_id' => $question_option->id,
                        'language' => $this->lang
                    ],
                    [
                        'option_text' => $data['option_text'] ?? '',
                        'explanation' => $data['explanation'] ?? ''
                    ]
                );
                TranslateModelJob::dispatch(QuestionOption::class, QuestionOptionTranslation::class, 'Question_id', $question_option->id, ['option_text', 'explanation'], $this->lang);
                $question_option = $question_option->refresh()->loadTranslation($this->lang);
                return $question_option;
            });
        } catch (\Exception $e) {
            Log::error('Question Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteQuestion(QuestionOption $question_option): bool
    {
        return $question_option->delete();
    }

    public function toggleStatus(QuestionOption $question_option): QuestionOption|null
    {
        $question_option->update(['status' => !$question_option->status, 'updated_by' => $this->user->id]);
        return $question_option->refresh();
    }
}
