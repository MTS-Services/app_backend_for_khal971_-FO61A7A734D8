<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    private $type;
    public function __construct($resource, $type = 'topic')
    {
        parent::__construct($resource);
        $this->type = $type;
    }
    public function toArray(Request $request): array
    {
        $lite = [
            'id' => $this->id,
            'order_index' => $this->order_index,
            'icon' => storage_url($this->course?->subject?->icon),
            'status' => $this->status_label,
            'statusList' => $this->status_list,
            'questions_count' => $this->questions_count ?? 0,
            'quizzes_count' => $this->quizzes_count ?? 0,
            'language' => translation($this->translations)->language ?? 'en',
            'name' => translation($this->translations)->name ?? 'Not Found',
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];

        $relations = ['course' => new CourseResource($this->whenLoaded('course'))];

        return match ($this->type) {
            'lite' => $lite,
            default => array_merge($lite, $relations),
        };

    }
}
