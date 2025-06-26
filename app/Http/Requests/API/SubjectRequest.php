<?php

namespace App\Http\Requests\API;

use App\Http\Requests\API\BaseRequest;

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
            'is_premium' => 'required|boolean',
        ] + ($this->isMethod('POST') ? $this->stote() : $this->update());
    }

    private function stote(): array
    {
        return [
            'name' => 'required|string|unique:subjects,name',
        ];
    }

    private function update(): array
    {
        return [
            'name' => "required|string|unique:subjects,name,{$this->route('subject')}",
        ];
    }
}
