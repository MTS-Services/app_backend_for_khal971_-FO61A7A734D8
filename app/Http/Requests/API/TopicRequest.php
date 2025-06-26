<?php

namespace App\Http\Requests\API;


class TopicRequest extends BaseRequest
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
            'course_id' => 'required|exists:subjects,id',
            'is_premium' => 'nullable|boolean',
        ]+($this->isMethod('POST') ? $this->stote() : $this->update());
    }

    public function stote(): array
    {
        return [
            'name' => 'required|string|unique:topics,name',
        ];
    }
    public function update(): array
    {
        return [
            'name' => 'required|string|unique:topics,name,' . $this->route('topic')->id,
        ];
    }
}
