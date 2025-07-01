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
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:1024',
            'point' => 'nullable|integer',
            'time_limit' => 'nullable|integer',
            'explanation' => 'nullable|string',
            // 'hints' => 'nullable|string',
            // 'tags' => 'nullable|string',
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
