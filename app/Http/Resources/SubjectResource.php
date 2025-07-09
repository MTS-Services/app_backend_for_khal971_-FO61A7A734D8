<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
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
            'icon' => storage_url($this->icon),
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'courses_count' => $this->courses_count,
            'topics_count' => $this->topics_count,
            'questions_count' => $this->questions_count,
            'quizzes_count' => $this->quizzes_count,
            'language' => translation($this->translations)->language,
            'name' => translation($this->translations)->name,
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",

        ];
    }
}
