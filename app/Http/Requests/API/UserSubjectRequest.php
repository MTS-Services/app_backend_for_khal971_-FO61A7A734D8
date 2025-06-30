<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserSubjectRequest extends FormRequest
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
            'subjects' => 'required|array|min:5',
            'subjects.*' => 'required|exists:subjects,id',
        ];
    }
     public function messages(): array
    {
        return [
            'subjects.required' => 'Please select at least one subject.',
            'subjects.max' => 'You can select up to 5 subjects.',
            'subjects.*.exists' => 'One or more selected subjects are invalid.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'token' => null,
            'data' => $validator->errors()->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
