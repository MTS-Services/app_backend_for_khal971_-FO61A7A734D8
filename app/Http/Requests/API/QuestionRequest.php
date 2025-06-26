<?php

namespace App\Http\Requests\API;


class QuestionRequest extends BaseRequest
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
            'question_type_id' => 'required|exists:question_types,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:1024',
            'points' => 'nullable|integer',
            'time_limit' => 'nullable|integer',
            'explanation' => 'nullable|string',
            'hints' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_premium' => 'nullable|boolean',
        ]+($this->isMethod('POST') ? $this->stote() : $this->update());
    }
    public function stote(): array
    {
        return [];
    }
    public function update(): array
    {
        return [];
    }
}
