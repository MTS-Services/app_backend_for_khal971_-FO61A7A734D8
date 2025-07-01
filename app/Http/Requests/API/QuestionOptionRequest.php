<?php

namespace App\Http\Requests\API;


class QuestionOptionRequest extends BaseRequest
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
            'question_id' => 'required|exists:questions,id',
            'question_option_id' => 'required|exists:questions,id',
            'option_text' => 'required|string',
            'explanation' => 'nullable|string',
        ]+($this->isMethod('POST') ? $this->stote() : $this->update());
    }
    public function stote()
    {
        return [];
    }
    public function update()
    {
        return [];
    }
}
