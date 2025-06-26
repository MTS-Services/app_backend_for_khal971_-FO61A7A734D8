<?php

namespace App\Http\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class QuestionService
{
    private $user;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function getQuestions(string $orderBy = 'order_index', string $direction = 'asc'): Builder
    {
        $query = Question::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->orderBy($orderBy, $direction)->latest();
    }
    public function getQuestion($param, string $query_field = 'id'): Question|null
    {
        $query = Question::query();
        if (!($this->user->is_premium || $this->user->is_admin)) {
            $query->free()->take(12);
        }
        return $query->where($query_field, $param)->first();
    }
    public function createQuestion($data): Question
    {
        $data['created_by'] = $this->user->id;
        return Question::create($data)->refresh();
    }
    public function updateQuestion(Question $question, $data): Question
    {
        $data['updated_by'] = $this->user->id;
        $question->update($data);
        return $question->refresh();
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
