<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use App\Http\Requests\API\BaseRequest;

class UserRequest extends BaseRequest
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
            'dob' => 'nullable|date',
            'gender' => 'nullable|numeric|between:0,2',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'school' => 'nullable|string',
            'user_class_id' => 'nullable|exists:user_classes,id',

            'image' => 'nullable|string',

            'updated_by' => 'nullable|exists:users,id',
        ]+($this->user()->is_admin ? $this->admin() : $this->userRequest());
    }
    public function admin(): array
    {
        return [
            
        ];
    }
    public function userRequest(): array
    {
        return [
            'username' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
        ];
    }
}
