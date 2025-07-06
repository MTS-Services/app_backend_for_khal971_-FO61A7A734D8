<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserProgressRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content_type' => 'required|string|in:subject,course,topic,quiz,question_set',
            'content_id' => 'required|integer',
            'total_items' => 'nullable|integer|min:0',
            'completed_items' => 'nullable|integer|min:0',
            'correct_items' => 'nullable|integer|min:0',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'accuracy_percentage' => 'nullable|numeric|min:0|max:100',
            'total_time_spent' => 'nullable|integer|min:0',
            'average_time_per_item' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:not_started,in_progress,completed,mastered',
            'first_accessed_at' => 'nullable|date',
            'last_accessed_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'current_streak' => 'nullable|integer|min:0',
            'best_streak' => 'nullable|integer|min:0',
            'last_activity_date' => 'nullable|date',
        ];
    }
}
