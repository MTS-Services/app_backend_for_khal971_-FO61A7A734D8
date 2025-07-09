<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_class' => $this->userClass?->name ?? null,
            'country' => $this->country,
            'city' => $this->city,
            'school' => $this->school,
            'dob' => $this->dob,
            'gender' => $this->gender_label,
            'genderList' => $this->gender_list,
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'image' => $this->image,
            'is_admin' => $this->is_admin,
            'is_premium' => $this->is_premium,
            'created_by' => $this->creater?->name ?? null,
            'updated_by' => $this->updater?->name ?? null,
        ];
    }
}
