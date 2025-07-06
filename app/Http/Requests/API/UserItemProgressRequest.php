<?php

namespace App\Http\Requests\API;

use App\Models\UserItemProgresss;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserItemProgressRequest extends BaseRequest
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
    // public function rules(): array
    // {
    //     return [
    //         'parent_progress_id' => 'required|exists:user_progress,id',
    //         'item_type' => ['required', 'string'],
    //         'item_id' => 'required|integer',
    //         'item_order' => 'nullable|integer',
    //         'status' => 'nullable|string|in:not_started,viewed,attempted,completed,correct,incorrect,skipped',
    //         'attempts' => 'nullable|integer',
    //         'correct_attempts' => 'nullable|integer',
    //         'time_spent' => 'nullable|integer',
    //         'first_accessed_at' => 'nullable|date',
    //         'last_accessed_at' => 'nullable|date',
    //         'completed_at' => 'nullable|date',
    //         'score' => 'nullable|numeric|between:0,100',
    //         'is_bookmarked' => 'nullable|boolean',
    //         'is_flagged' => 'nullable|boolean',
    //         'notes' => 'nullable|string',
    //     ];
    // }
    // public function store(): array
    // {
    //     return [
    //         'item_id' => [
    //             'required',
    //             'integer',
    //             Rule::unique('user_item_progress')
    //                 ->where(function ($query) {
    //                     return $query
    //                         ->where('user_id', Auth::id()) // or $this->user_id
    //                         ->where('item_type', $this->item_type);
    //             })
    //         ]
    //     ];
    // }
    // public function update(): array
    // {
    //     $progress = $this->route('user_item_progress');
    //     return [
    //     'item_id' => [
    //         'required',
    //         'integer',
    //         Rule::unique('user_item_progress')
    //             ->where(function ($query) {
    //                 return $query
    //                     ->where('user_id', Auth::id()) // or $this->user_id if coming from request
    //                     ->where('item_type', $this->item_type);
    //             })
    //             ->ignore($progress->id), // assumes route parameter name is {id}
    //     ],
    // ];
    // }

public function rules(): array
    {
        return [
            'parent_progress_id' => 'nullable|integer|exists:user_progress,id',
            'item_id' => 'required|integer|min:1',
            'item_order' => 'nullable|integer|min:0',
            'attempts' => 'nullable|integer|min:0',
            'correct_attempts' => 'nullable|integer|min:0',
            'time_spent' => 'nullable|integer|min:0',
            'score' => 'nullable|numeric|min:0|max:100',
            'is_bookmarked' => 'nullable|boolean',
            'is_flagged' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]+($this->isMethod('POST') ? $this->store()  : $this->update());
    }
    public function store(): array
    {
        return [
            'item_type' => 'required|string|in:' . implode(',', UserItemProgresss::getItemTypes()),
            'status' => 'required|integer|in:' . implode(',', UserItemProgresss::getStatuses()),
        ];
    }


 public function update(): array
    {
        return [
            'status' => 'required|integer|in:' . implode(',', UserItemProgresss::getStatuses()),
        ];
    }


// class BulkUpdateProgressRequest extends FormRequest
// {
//     public function rules(): array
//     {
//         return [
//             'items' => 'required|array|min:1|max:50',
//             'items.*.parent_progress_id' => 'nullable|integer|exists:user_progress,id',
//             'items.*.item_type' => 'required|string|in:' . implode(',', UserItemProgress::getItemTypes()),
//             'items.*.item_id' => 'required|integer|min:1',
//             'items.*.item_order' => 'nullable|integer|min:0',
//             'items.*.status' => 'required|integer|in:' . implode(',', UserItemProgress::getStatuses()),
//             'items.*.attempts' => 'nullable|integer|min:0',
//             'items.*.correct_attempts' => 'nullable|integer|min:0',
//             'items.*.time_spent' => 'nullable|integer|min:0',
//             'items.*.score' => 'nullable|numeric|min:0|max:100',
//             'items.*.is_bookmarked' => 'nullable|boolean',
//             'items.*.is_flagged' => 'nullable|boolean',
//             'items.*.notes' => 'nullable|string|max:1000',
//         ];
//     }

//     /**
//      * Get custom messages for validator errors.
//      */
//     public function messages(): array
//     {
//         $statusLabels = UserItemProgress::getStatusLabels();
//         return [
//             'items.*.parent_progress_id.exists' => 'One or more parent progress IDs do not exist.',
//             'items.*.item_type.in' => 'All item types must be one of: ' . implode(', ', UserItemProgress::getItemTypes()),
//             'items.*.status.in' => 'All status values must be one of: ' . implode(', ', array_values($statusLabels)),
//             'items.*.score.min' => 'All scores must be at least 0.',
//             'items.*.score.max' => 'All scores must not exceed 100.',
//             'items.*.notes.max' => 'All notes must not exceed 1000 characters.',
//         ];
//     }

//     /**
//      * Configure the validator instance.
//      */
//     public function withValidator($validator): void
//     {
//         $validator->after(function ($validator) {
//             $items = $this->input('items', []);
            
//             foreach ($items as $index => $item) {
//                 // Validate correct_attempts is not greater than attempts for each item
//                 if (isset($item['attempts']) && isset($item['correct_attempts'])) {
//                     if ($item['correct_attempts'] > $item['attempts']) {
//                         $validator->errors()->add("items.{$index}.correct_attempts", 'Correct attempts cannot be greater than total attempts.');
//                     }
//                 }
//             }
//         });
//     }
   
    
// }
}