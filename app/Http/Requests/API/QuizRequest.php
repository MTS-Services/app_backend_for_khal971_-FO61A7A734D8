<?php

namespace App\Http\Requests\API;


class QuizRequest extends BaseRequest
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
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ];
    }
}
