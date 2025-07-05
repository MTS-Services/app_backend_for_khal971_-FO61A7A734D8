<?php

namespace App\Http\Requests\API;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserItemProgressRequest extends BaseRequest
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
            'parent_progress_id' => 'required|exists:user_progress,id',
            'item_type' => ['required', 'string'],
            'item_id' => 'required|integer',
            'item_order' => 'nullable|integer',
            'status' => 'nullable|string|in:not_started,viewed,attempted,completed,correct,incorrect,skipped',
            'attempts' => 'nullable|integer',
            'correct_attempts' => 'nullable|integer',
            'time_spent' => 'nullable|integer',
            'first_accessed_at' => 'nullable|date',
            'last_accessed_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'score' => 'nullable|numeric|between:0,100',
            'is_bookmarked' => 'nullable|boolean',
            'is_flagged' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ];
    }
    public function store(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                Rule::unique('user_item_progresses')
                    ->where(function ($query) {
                        return $query
                            ->where('user_id', Auth::id()) // or $this->user_id
                            ->where('item_type', $this->item_type);
                })
            ]
        ];
    }
    public function update(): array
    {
        $progress = $this->route('user_item_progress');
        return [
        'item_id' => [
            'required',
            'integer',
            Rule::unique('user_item_progresses')
                ->where(function ($query) {
                    return $query
                        ->where('user_id', Auth::id()) // or $this->user_id if coming from request
                        ->where('item_type', $this->item_type);
                })
                ->ignore($progress->id), // assumes route parameter name is {id}
        ],
    ];
    }
}
