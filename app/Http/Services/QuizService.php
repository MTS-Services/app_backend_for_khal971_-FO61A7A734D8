<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizService
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
     * Fetch Quizs, optionally filtered and ordered.
     *
     * @param  string  $direction asc|desc default: asc
     * @return Builder
     */
    public function getQuizzes(string $orderBy = 'order_index', string $direction = 'asc')
    {

        $query = Quiz::translation($this->lang);
        // if (!($this->user->is_admin)) {
        //     $query->free()->take(12);
        // }
        return $query->orderBy($orderBy, $direction)->latest();
    }

    public function getQuiz($param, string $query_field = 'id'): Quiz|null
    {

        $query = Quiz::translation($this->lang);
        if (!( $this->user->is_admin)) {
            $query->free();
        }
        $quiz = $query->where($query_field, $param)->first();
        return $quiz;
    }
    public function createQuiz($data): Quiz|null
    {
        try {
            $data['created_by'] = $this->user->id;

            return DB::transaction(function () use ($data) {
                $quiz = Quiz::create($data);
                QuizTranslation::create([
                    'quiz_id' => $quiz->id,
                    'language' => $this->lang,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? ''
                ]);
                TranslateModelJob::dispatch( Quiz::class, QuizTranslation::class, 'quiz_id', $quiz->id, ['title', 'description'], $this->lang);
                $quiz = $quiz->refresh()->loadTranslation($this->lang);
                return $quiz;
            });
        } catch (\Exception $e) {
            Log::error(' Quiz Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateQuiz( Quiz $quiz, $data): Quiz|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($quiz, $data) {
                $quiz->update($data);
                QuizTranslation::updateOrCreate(['quiz_id' => $quiz->id, 'language' => $this->lang], 
                [
                    'title' => $data['title'] ?? '',
                    'description' => $data['description'] ?? '',
                ]);
                TranslateModelJob::dispatch( Quiz::class, QuizTranslation::class, 'quiz_id', $quiz->id, ['title', 'description'], $this->lang);
                $quiz = $quiz->refresh()->loadTranslation($this->lang);
                return $quiz;
            });
        } catch (\Exception $e) {
            Log::error('Quiz Update Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteQuiz( Quiz $quiz): bool
    {
        return $quiz->delete();
    }

    public function toggleStatus( Quiz $quiz): Quiz|null
    {
        $quiz->update(['status' => !$quiz->status, 'updated_by' => $this->user->id]);
        return $quiz->refresh();
    }
}
