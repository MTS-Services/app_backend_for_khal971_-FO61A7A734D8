<?php

namespace App\Http\Requests\API;

use App\Models\ProgressMilestoneTranslation;
use Illuminate\Validation\Rule;

class ProgressMilestoneRequest extends BaseRequest
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
            'threshold_value' => 'required|numeric|min:0',
            'requirement_description' => 'required|string|max:65535',
            'badge_name' => 'nullable|string|max:100',
            'badge_icon' => 'nullable|string|',
            'points_reward' => 'nullable|integer|min:0',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:65535',
            'celebration_message' => 'nullable|string|max:65535',
            'is_active' => 'nullable|boolean',
            'order_index' => 'nullable|integer|min:0',
        ]+($this->isMethod('POST') ? $this->store()  : $this->update());
    }
    public function store(): array
    {
        return [
            'content_type' => [
                'required',
                'string',
                Rule::in(ProgressMilestoneTranslation::getContentTypes())
            ],
            'milestone_type' => [
                'required',
                'string',
                Rule::in(ProgressMilestoneTranslation::getMilestoneTypes())
            ],
        ];
    }
    public function update(): array
    {
        return [
            'content_type' => [
                'nullable',
                'string',
                Rule::in(ProgressMilestoneTranslation::getContentTypes())
            ],
            'milestone_type' => [
                'nullable',
                'string',
                Rule::in(ProgressMilestoneTranslation::getMilestoneTypes())
            ]
        ];
    }
}
