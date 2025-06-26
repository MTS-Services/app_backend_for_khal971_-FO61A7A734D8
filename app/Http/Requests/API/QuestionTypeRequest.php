<?php

namespace App\Http\Requests\API;


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
        ]+ ($this->isMethod('POST') ? $this->store(): $this->update());
    }
    public function store()
    {
        return [
            'name' => 'required|string|unique:question_types,name',
        ];
    }
    public function update()
    {
        return [
            'name' => 'required|string|unique:question_types,name,' . $this->route('question_type')->id,
        ];
    }
}
