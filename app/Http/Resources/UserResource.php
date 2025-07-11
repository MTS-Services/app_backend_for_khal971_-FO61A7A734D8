<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'user_class' => $this->userClass?->name ?? "N/A",
            'country' => $this->country ?? "N/A",
            'city' => $this->city ?? "N/A",
            'school' => $this->school ?? "N/A",
            'dob' => $this->dob ?? "N/A",
            'gender' => $this->gender_label,
            'genderList' => $this->gender_list,
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'image' => storage_url($this->image),
            'is_admin' => $this->is_admin,
            'is_premium' => $this->is_premium,
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];
    }
}
