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
            'question_details_id' => 'required|exists:question_details,id',
            'title' => 'required|string',
            'answer' => 'required|string', 
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
