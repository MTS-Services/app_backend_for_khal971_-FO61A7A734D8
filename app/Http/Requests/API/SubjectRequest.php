<?php

namespace App\Http\Requests\API;

use App\Http\Requests\API\BaseRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends BaseRequest
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
            'icon' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:1024',
        ] + ($this->isMethod('POST') ? $this->store() : $this->update());
    }

    private function store(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('subject_translations')->where(
                    fn($query) => $query->where('language', defaultLang())
                ),
            ],
        ];
    }

    private function update(): array
    {
        $subjectId = $this->route('subject');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('subject_translations')->where(
                    fn($query) => $query
                        ->where('language', defaultLang())
                        ->where('subject_id', '!=', $subjectId)
                ),
            ],
        ];
    }
}
