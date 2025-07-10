<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserSubjectRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subjects' => 'required|array|min:5',
            'subjects.*' => 'required|exists:subjects,id',
        ];
    }
    public function messages(): array
    {
        return [
            'subjects.required' => 'Subjects are required.',
            'subjects.min' => 'Please select at least 5 subjects.',
            'subjects.*.exists' => 'One or more selected subjects are invalid.',
        ];
    }
}



