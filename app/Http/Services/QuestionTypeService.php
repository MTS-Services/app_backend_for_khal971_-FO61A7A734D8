<?php

namespace App\Http\Services;

use App\Models\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuestionTypeService
{
    private $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getQuestionTypes(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = QuestionType::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestionType($param, string $query_field = 'id'): QuestionType|null
    {
        $query = QuestionType::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestionType($data): QuestionType
    {
        $data['created_by'] = $this->user->id;
        return QuestionType::create($data)->refresh();
    }
    public function updateQuestionType(QuestionType $question_type, $data): QuestionType
    {
        $data['updated_by'] = $this->user->id;
        $question_type->update($data);
        return $question_type->refresh();
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
