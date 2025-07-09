<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'topics_count' => $this->topics_count ?? 0,
            'questions_count' => $this->questions_count ?? 0,
            'quizzes_count' => $this->quizzes_count ?? 0,
            'language' => translation($this->translations)->language,
            'name' => translation($this->translations)->name,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",

        ];
    }
}
