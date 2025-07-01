<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;

class QuestionTypeRequest extends BaseRequest
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
            'description' => 'nullable',
        ] + ($this->isMethod('POST') ? $this->store() : $this->update());
    }
    public function store()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('question_type_translations', 'name')->where(
                    fn($query) => $query->where('language', defaultLang())
                ),
            ],
        ];
    }
    public function update()
    {
        $questionTypeId = $this->route('question_type');
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('question_type_translations', 'name')->where(
                    fn($query) => $query
                        ->where('language', defaultLang())
                        ->where('question_type_id', '!=', $questionTypeId)
                ),
            ]
        ];
    }
}
