<?php

namespace App\Http\Requests\API;

class CourseRequest extends BaseRequest
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
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'is_premium' => 'nullable|boolean',
        ]+($this->isMethod('POST') ? $this->stote() : $this->update());
    }
    private function stote(): array
    {
        return [
        ];
    }

    private function update(): array
    {
        return [
        ];
    }
}
