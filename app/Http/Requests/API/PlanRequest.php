<?php

namespace App\Http\Requests\API;


class PlanRequest extends BaseRequest
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
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'duration'          => 'required|integer|min:1',
            'stripe_price_id'   => 'nullable|string|max:255',
            'apple_product_id'  => 'nullable|string|max:255',
            'google_product_id' => 'nullable|string|max:255',
            'features'          => 'nullable|array',
            'features'          => 'string',
            // 'status'            => 'required|integer|in:0,1',
            'is_popular'        => 'boolean',
        ];
    }
}
