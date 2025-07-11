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
            'language' => translation($this->translations)?->language ?? "Not Found",
            'name' => translation($this->translations)?->name ?? "Not Found",
            'created_at' => $this->created_at_formatted ?? dateTimeFormat(Carbon::now()),
            'updated_at' => $this->updated_at_formatted ?? "N/A",
            'created_by' => $this->creater?->name ?? "System",
            'updated_by' => $this->updater?->name ?? "N/A",
        ];

        $relations = ['course' => new CourseResource($this->whenLoaded('course'))];

        $practices = [
            'total_attempts' => $this->practice && $this->practice->total_attempts ? $this->practice->total_attempts : 0,
            'correct_attempts' => $this->practice && $this->practice->correct_attempts ? $this->practice->correct_attempts : 0,
            'wrong_attempts' => $this->practice && $this->practice->wrong_attempts ? $this->practice->wrong_attempts : 0,
            'progress' => $this->practice && $this->practice->progress ? $this->practice->progress : 0,
            'progress_status' => $this->practice && $this->practice->status ? $this->practice->status_label : 'Not Started',
        ];

        return match ($this->type) {
            'lite' => $lite,
            'practice' => array_merge($lite, $practices),
            default => array_merge($lite, $practices, $relations),
        };

    }
}
