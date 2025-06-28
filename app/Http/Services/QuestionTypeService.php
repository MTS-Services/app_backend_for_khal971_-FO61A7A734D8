<?php

namespace App\Http\Services;

use App\Jobs\TranslateModelJob;
use App\Models\QuestionType;
use App\Models\QuestionTypeTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionTypeService
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

    public function getQuestionTypes(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = QuestionType::translation($this->lang);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestionType($param, string $query_field = 'id'): QuestionType|null
    {
        $query = QuestionType::translation($this->lang);
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestionType($data): QuestionType|null
    {
        try{
            $data['created_by'] = $this->user->id;
            return DB::transaction(function () use ($data) {
                $question_type = QuestionType::create($data);
                QuestionTypeTranslation::create(['question_type_id' => $question_type->id, 'language' => $this->lang, 'name' => $data['name'], 'description' => $data['description']]);
                TranslateModelJob::dispatch(QuestionType::class, QuestionTypeTranslation::class, 'question_type_id', $question_type->id, ['name', 'description'], $this->lang);
                $question_type = $question_type->refresh()->loadTranslation($this->lang);
                return $question_type;
            });
        }catch(\Exception $e){
            Log::error('QuestionType Create Error: ' . $e->getMessage());
            return null;
        }
    }
    public function updateQuestionType(QuestionType $question_type, $data): QuestionType|null
    {
        try{
            $data['updated_by'] = $this->user->id;
            return DB::transaction(function () use ($question_type, $data) {
                $question_type->update($data);
                QuestionTypeTranslation::updateOrCreate(['question_type_id' => $question_type->id, 'language' => $this->lang], ['name' => $data['name'], 'description' => $data['description']]);
                TranslateModelJob::dispatch(QuestionType::class, QuestionTypeTranslation::class, 'question_type_id', $question_type->id, ['name', 'description'], $this->lang);
                $question_type = $question_type->refresh()->loadTranslation($this->lang);
                return $question_type;
            });
        }catch(\Exception $e){
            Log::error('QuestionType Update Error: ' . $e->getMessage());
            return null;
        }
    }
    public function deleteQuestionType(QuestionType $question_type): bool
    {
        return $question_type->delete();
    }
    public function toggleStatus(QuestionType $question_type): QuestionType|null
    {
        $newStatus = $question_type->status === QuestionType::STATUS_INACTIVE
            ? QuestionType::STATUS_ACTIVE
            : QuestionType::STATUS_INACTIVE;

        $question_type->update([
            'status' => $newStatus,
            'updated_by' => $this->user->id,
        ]);

        return $question_type->refresh();
    }
}
