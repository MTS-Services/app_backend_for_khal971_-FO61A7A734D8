<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;

class UserClassRequest extends BaseRequest
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
        ]+ ($this->isMethod('POST') ? $this->stote() : $this->update());
    }
    public function stote(): array
    {
        return [
            'name' => 'required|string|unique:user_classes,name',
        ];
    }
    public function update(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('user_classes', 'name')->ignore($this->route('user_class')),
            ],
        ];
    }
}
