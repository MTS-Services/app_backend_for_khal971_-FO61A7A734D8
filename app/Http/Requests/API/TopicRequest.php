<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;

class TopicRequest extends BaseRequest
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
            'course_id' => 'required|exists:subjects,id',
            'is_premium' => 'nullable|boolean',
        ]+($this->isMethod('POST') ? $this->stote() : $this->update());
    }

    public function stote(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('topic_translations')->where(
                    fn($query) => $query->where('language', defaultLang())
                ),],
        ];
    }
    public function update(): array
    {
        $topicId = $this->route('topic');
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('topic_translations')->where(
                    fn($query) => $query
                        ->where('language', defaultLang())
                        ->where('topic_id', '!=', $topicId)
                ),
            ]
        ];
    }
}
