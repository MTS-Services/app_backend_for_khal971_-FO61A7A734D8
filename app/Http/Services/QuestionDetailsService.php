<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\QuestionDetails;
use App\Models\QuestionDetailsTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionDetailsService
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
    public function getQuestionDetails(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = QuestionDetails::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestionDetail($param, string $query_field = 'id'): QuestionDetails|null
    {
        $query = QuestionDetails::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestion($data): QuestionDetails|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question_details = QuestionDetails::create($data);
                QuestionDetailsTranslation::create([
                    'question_id' => $question_details->id,
                    'language' => $this->lang,
                    'title' => $data['title'] ?? '',
                    'answer' => $data['answer'] ?? ''
                ]);
                TranslateModelJob::dispatch(QuestionDetails::class, QuestionDetailsTranslation::class, 'question_id', $question_details->id, ['title', 'answer'], $this->lang);
                $question_details = $question_details->refresh()->loadTranslation($this->lang);
                return $question_details;
            });
        } catch (\Exception $e) {
            Log::error('Question Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuestion(QuestionDetails $question_details, $data): QuestionDetails|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            $question_details->update($data);
            QuestionDetailsTranslation::updateOrCreate(
                ['question_id' => $question_details->id, 'language' => $this->lang], // condition
                [
                    'title' => $data['title'] ?? '',
                    'answer' => $data['answer'] ?? '',
                ]
            );

            TranslateModelJob::dispatch(QuestionDetails::class, QuestionDetailsTranslation::class, 'question_id', $question_details->id, ['title', 'answer'], $this->lang);
            $question_details = $question_details->refresh()->loadTranslation($this->lang);
            return $question_details;
        } catch (\Exception $e) {
            Log::error('Question Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuestion(QuestionDetails $question_details): void
    {
        $question_details->delete();
    }
    public function toggleStatus(QuestionDetails $question_details): QuestionDetails
    {
        $question_details->update(['status' => !$question_details->status, 'updated_by' => $this->user->id]);
        return $question_details->refresh();
    }
}
