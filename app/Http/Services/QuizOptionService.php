<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\QuizOpitonTranslation;
use App\Models\QuizOption;
use App\Models\QuizOptionTranslation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizOptionService
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
    public function getQuizOptions(string $orderBy = 'order_index', string $direction = 'asc')
    {
        $query = QuizOption::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuizOption($param, string $query_field = 'id'): QuizOption|null
    {
        $query = QuizOption::translation($this->lang);
        if (!($this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuizOption(array $data): QuizOption|null
    {
        try {
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $quiz_option = QuizOption::create($data);
                QuizOptionTranslation::create([
                    'quiz_option_id' => $quiz_option->id,
                    'language' => $this->lang,
                    'title' => $data['title'] ?? '',
                ]);
                TranslateModelJob::dispatch(QuizOption::class, QuizOptionTranslation::class, 'quiz_option_id', $quiz_option->id, ['title'], $this->lang);
                $quiz_option = $quiz_option->refresh()->loadTranslation($this->lang);
                return $quiz_option;
            });
        } catch (\Exception $e) {
            Log::error('QuizOption Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuizOption(array $data, QuizOption $quiz_option): QuizOption|null
    {
        try {
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($data, $quiz_option) {
                $quiz_option->update($data);
                QuizOptionTranslation::where('quiz_option_id', $quiz_option->id)->where('language', $this->lang)->update([
                    'title' => $data['title'] ?? '',
                ]);
                TranslateModelJob::dispatch(QuizOption::class, QuizOptionTranslation::class, 'quiz_option_id', $quiz_option->id, ['title'], $this->lang);
                $quiz_option = $quiz_option->refresh()->loadTranslation($this->lang);
                return $quiz_option;
            });
        } catch (\Exception $e) {
            Log::error('QuizOption Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuizOption(QuizOption $quiz_option): void
    {
        $quiz_option->delete();
    }
    public function toggleStatus(QuizOption $quiz_option): QuizOption|null
    {
        $quiz_option->update(['status' => !$quiz_option->status, 'updated_by' => $this->user->id]);
        return $quiz_option->refresh();
    }
}
