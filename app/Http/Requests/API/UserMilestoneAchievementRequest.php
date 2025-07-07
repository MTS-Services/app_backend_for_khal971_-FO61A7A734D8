<?php

namespace App\Http\Requests\API;


class UserMilestoneAchievementRequest extends BaseRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'milestone_id' => ['required', 'exists:progress_milestones,id'],
            'progress_id' => ['required', 'exists:user_progress,id'],
            'achieved_value' => ['required', 'numeric'],
            'achieved_at' => ['nullable', 'date'],
            'is_notified' => ['nullable', 'boolean'],
            'notification_sent_at' => ['nullable', 'date'],
        ];
    }
}
