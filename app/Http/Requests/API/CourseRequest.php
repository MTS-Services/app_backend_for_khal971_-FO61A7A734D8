<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;

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
        ]+($this->isMethod('POST') ? $this->store() : $this->update());
    }
    private function store(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('course_translations')->where(
                    fn($query) => $query->where('language', defaultLang())
                ),
            ],
        ];
    }

    private function update(): array
    {
        $courese = $this->route('course');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('course_translations')->where(
                    fn($query) => $query
                        ->where('language', defaultLang())
                        ->where('course_id', '!=', $courese->id)
                ),
            ],
        ];
    }
}
